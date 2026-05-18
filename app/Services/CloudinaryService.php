<?php

namespace App\Services;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Exception;

class CloudinaryService
{
    protected UploadApi $uploadApi;

    public function __construct()
    {
        $cloudinaryUrl = env('CLOUDINARY_URL');
        if (!$cloudinaryUrl) {
            throw new Exception("CLOUDINARY_URL environment variable is not configured.");
        }

        // Initialize Cloudinary Configuration
        Configuration::instance($cloudinaryUrl);
        $this->uploadApi = new UploadApi();
    }

    /**
     * Upload an Audio File (MP3) to Cloudinary
     * 
     * @param UploadedFile $file
     * @return array Contains 'secure_url' and 'duration'
     */
    public function uploadAudio(UploadedFile $file): array
    {
        try {
            $response = $this->uploadApi->upload($file->getRealPath(), [
                'resource_type' => 'video', // Cloudinary classifies audio as 'video' resource type
                'folder' => 'webmusic/audio',
                'overwrite' => true,
            ]);

            if (!isset($response['secure_url'])) {
                throw new Exception("Upload failed, secure_url not returned: " . json_encode($response));
            }

            return [
                'secure_url' => $response['secure_url'],
                'duration' => $response['duration'] ?? 0, // duration in seconds returned by Cloudinary analysis!
                'public_id' => $response['public_id'],
            ];
        } catch (Exception $e) {
            throw new Exception("Cloudinary Audio Upload Failed: " . $e->getMessage());
        }
    }

    /**
     * Upload an Image File (Thumbnail) to Cloudinary
     * 
     * @param UploadedFile $file
     * @return array Contains 'secure_url'
     */
    public function uploadImage(UploadedFile $file): array
    {
        try {
            $response = $this->uploadApi->upload($file->getRealPath(), [
                'resource_type' => 'image',
                'folder' => 'webmusic/thumbnails',
                'overwrite' => true,
            ]);

            if (!isset($response['secure_url'])) {
                throw new Exception("Upload failed, secure_url not returned: " . json_encode($response));
            }

            return [
                'secure_url' => $response['secure_url'],
                'public_id' => $response['public_id'],
            ];
        } catch (Exception $e) {
            throw new Exception("Cloudinary Image Upload Failed: " . $e->getMessage());
        }
    }

    /**
     * Delete a file from Cloudinary by Public ID
     */
    public function deleteFile(string $publicId, string $resourceType = 'image'): bool
    {
        try {
            $this->uploadApi->destroy($publicId, [
                'resource_type' => $resourceType
            ]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Cloudinary File Deletion Failed: " . $e->getMessage());
        }
    }
}
