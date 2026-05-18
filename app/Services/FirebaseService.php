<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class FirebaseService
{
    protected string $projectId;
    protected string $clientEmail;
    protected string $privateKey;

    public function __construct()
    {
        $credentialsPath = storage_path('app/firebase/music-web-e717c-firebase-adminsdk-fbsvc-9ca60a1147.json');
        
        if (!file_exists($credentialsPath)) {
            throw new Exception("Firebase service account credentials file not found at: " . $credentialsPath);
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true);
        $this->projectId = $credentials['project_id'];
        $this->clientEmail = $credentials['client_email'];
        $this->privateKey = $credentials['private_key'];
    }

    /**
     * Get OAuth2 Access Token for Firestore API (Cached)
     */
    public function getAccessToken(): string
    {
        return Cache::remember('firebase_oauth_token', 3300, function () {
            $now = time();
            $payload = [
                'iss' => $this->clientEmail,
                'sub' => $this->clientEmail,
                'scope' => 'https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/userinfo.email',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            // Sign assertion token using standard OpenSSL RS256
            $jwt = JWT::encode($payload, $this->privateKey, 'RS256');

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->failed()) {
                throw new Exception("Failed to generate Google OAuth2 token: " . $response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * Verify Firebase ID Token (JWT) sent from Client
     */
    public function verifyIdToken(string $idToken): array
    {
        // 1. Fetch Google's public certificates
        $publicKeys = Cache::remember('firebase_public_keys', 86400, function () {
            $response = Http::get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
            if ($response->failed()) {
                throw new Exception("Failed to fetch Google public keys.");
            }
            return $response->json();
        });

        // 2. Wrap keys in Firebase JWT Key classes
        $keys = [];
        foreach ($publicKeys as $kid => $cert) {
            $keys[$kid] = new Key($cert, 'RS256');
        }

        // 3. Decode and verify JWT
        try {
            $decoded = JWT::decode($idToken, $keys);
            
            // Validate audience and issuer
            if ($decoded->aud !== $this->projectId) {
                throw new Exception("Invalid audience.");
            }
            if ($decoded->iss !== "https://securetoken.google.com/" . $this->projectId) {
                throw new Exception("Invalid issuer.");
            }

            return (array) $decoded;
        } catch (Exception $e) {
            throw new Exception("Firebase Token Verification Failed: " . $e->getMessage());
        }
    }

    /**
     * Map basic array into Firestore REST Document fields format
     */
    protected function mapToFirestoreFields(array $data): array
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_int($value)) {
                $fields[$key] = ['integerValue' => (string) $value];
            } elseif (is_double($value) || is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_array($value)) {
                // If it is associative, map to mapValue, else listValue
                if (array_keys($value) !== range(0, count($value) - 1)) {
                    $fields[$key] = ['mapValue' => ['fields' => $this->mapToFirestoreFields($value)]];
                } else {
                    $values = [];
                    foreach ($value as $item) {
                        if (is_array($item)) {
                            $values[] = ['mapValue' => ['fields' => $this->mapToFirestoreFields($item)]];
                        } else {
                            $values[] = ['stringValue' => (string) $item];
                        }
                    }
                    $fields[$key] = ['listValue' => ['values' => $values]];
                }
            } else {
                $fields[$key] = ['stringValue' => (string) $value];
            }
        }
        return $fields;
    }

    /**
     * Map Firestore REST Document fields back into standard PHP array
     */
    public function mapFromFirestoreFields(array $fields): array
    {
        $data = [];
        foreach ($fields as $key => $wrapper) {
            if (isset($wrapper['stringValue'])) {
                $data[$key] = $wrapper['stringValue'];
            } elseif (isset($wrapper['booleanValue'])) {
                $data[$key] = (bool) $wrapper['booleanValue'];
            } elseif (isset($wrapper['integerValue'])) {
                $data[$key] = (int) $wrapper['integerValue'];
            } elseif (isset($wrapper['doubleValue'])) {
                $data[$key] = (float) $wrapper['doubleValue'];
            } elseif (isset($wrapper['nullValue'])) {
                $data[$key] = null;
            } elseif (isset($wrapper['mapValue']['fields'])) {
                $data[$key] = $this->mapFromFirestoreFields($wrapper['mapValue']['fields']);
            } elseif (isset($wrapper['listValue']['values'])) {
                $items = [];
                foreach ($wrapper['listValue']['values'] as $itemWrapper) {
                    if (isset($itemWrapper['mapValue']['fields'])) {
                        $items[] = $this->mapFromFirestoreFields($itemWrapper['mapValue']['fields']);
                    } elseif (isset($itemWrapper['stringValue'])) {
                        $items[] = $itemWrapper['stringValue'];
                    }
                }
                $data[$key] = $items;
            }
        }
        return $data;
    }

    /**
     * Get Firestore Document by Collection and Document ID
     */
    public function getDocument(string $collection, string $documentId): ?array
    {
        $accessToken = $this->getAccessToken();
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";

        $response = Http::withToken($accessToken)->get($url);

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new Exception("Firestore GET Failed: " . $response->body());
        }

        $fields = $response->json('fields') ?? [];
        return $this->mapFromFirestoreFields($fields);
    }

    /**
     * Set/Create/Overwrite Firestore Document
     */
    public function setDocument(string $collection, string $documentId, array $data): array
    {
        $accessToken = $this->getAccessToken();
        // Use PATCH to create or overwrite/merge
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";

        $fields = $this->mapToFirestoreFields($data);

        $response = Http::withToken($accessToken)->patch($url, [
            'fields' => $fields
        ]);

        if ($response->failed()) {
            throw new Exception("Firestore PATCH Failed: " . $response->body());
        }

        return $this->mapFromFirestoreFields($response->json('fields') ?? []);
    }

    /**
     * Create document with auto-generated ID in collection
     */
    public function createDocument(string $collection, array $data): array
    {
        $accessToken = $this->getAccessToken();
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

        $fields = $this->mapToFirestoreFields($data);

        $response = Http::withToken($accessToken)->post($url, [
            'fields' => $fields
        ]);

        if ($response->failed()) {
            throw new Exception("Firestore POST Failed: " . $response->body());
        }

        $docName = $response->json('name');
        $parts = explode('/', $docName);
        $docId = end($parts);

        $result = $this->mapFromFirestoreFields($response->json('fields') ?? []);
        $result['id'] = $docId;
        return $result;
    }

    /**
     * Delete Firestore Document
     */
    public function deleteDocument(string $collection, string $documentId): bool
    {
        $accessToken = $this->getAccessToken();
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";

        $response = Http::withToken($accessToken)->delete($url);

        if ($response->failed()) {
            throw new Exception("Firestore DELETE Failed: " . $response->body());
        }

        return true;
    }

    /**
     * List all documents in a collection
     */
    public function listDocuments(string $collection): array
    {
        $accessToken = $this->getAccessToken();
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";

        $response = Http::withToken($accessToken)->get($url);

        if ($response->failed()) {
            throw new Exception("Firestore LIST Failed: " . $response->body());
        }

        $documents = $response->json('documents') ?? [];
        $result = [];

        foreach ($documents as $doc) {
            $parts = explode('/', $doc['name']);
            $id = end($parts);
            
            $data = $this->mapFromFirestoreFields($doc['fields'] ?? []);
            $data['id'] = $id;
            $result[] = $data;
        }

        return $result;
    }
}
