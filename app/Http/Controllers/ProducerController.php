<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Services\CloudinaryService;
use Inertia\Inertia;
use Exception;

class ProducerController extends Controller
{
    protected FirebaseService $firebase;
    protected CloudinaryService $cloudinary;

    public function __construct(FirebaseService $firebase, CloudinaryService $cloudinary)
    {
        $this->firebase = $firebase;
        $this->cloudinary = $cloudinary;
    }

    /**
     * Show Producer Dashboard (lists producer's songs)
     */
    public function dashboard()
    {
        $user = session('user');
        
        try {
            // Fetch all songs and filter for current producer
            $allSongs = $this->firebase->listDocuments('songs');
            $mySongs = array_values(array_filter($allSongs, function ($song) use ($user) {
                return ($song['producer_id'] ?? '') === $user['uid'];
            }));

            return Inertia::render('Producer/Dashboard', [
                'songs' => $mySongs
            ]);
        } catch (Exception $e) {
            return Inertia::render('Producer/Dashboard', [
                'songs' => [],
                'error' => 'Failed to fetch songs: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show Upload Form
     */
    public function showUpload()
    {
        return Inertia::render('Producer/Upload');
    }

    /**
     * Handle Song Upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a|max:20480', // max 20MB
            'thumbnail' => 'required|file|image|max:5120', // max 5MB
        ]);

        $user = session('user');

        try {
            // 1. Upload audio to Cloudinary
            $audioUpload = $this->cloudinary->uploadAudio($request->file('audio'));

            // 2. Upload thumbnail image to Cloudinary
            $thumbnailUpload = $this->cloudinary->uploadImage($request->file('thumbnail'));

            // 3. Store song metadata in Firestore
            $songData = [
                'title' => $request->title,
                'genre' => $request->genre,
                'duration' => (int) round($audioUpload['duration']),
                'audio_url' => $audioUpload['secure_url'],
                'thumbnail_url' => $thumbnailUpload['secure_url'],
                'audio_public_id' => $audioUpload['public_id'],
                'thumbnail_public_id' => $thumbnailUpload['public_id'],
                'producer_id' => $user['uid'],
                'producer_name' => $user['name'],
                'plays_count' => 0,
                'likes_count' => 0,
                'is_moderated' => false,
                'created_at' => date('c'),
            ];

            $this->firebase->createDocument('songs', $songData);

            return redirect()->route('producer.dashboard')->with('success', 'Track uploaded successfully!');

        } catch (Exception $e) {
            return back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle Song Deletion
     */
    public function delete($songId)
    {
        $user = session('user');

        try {
            // 1. Get Song metadata
            $song = $this->firebase->getDocument('songs', $songId);

            if (!$song) {
                return back()->withErrors(['message' => 'Track not found.']);
            }

            // Enforce ownership
            if ($song['producer_id'] !== $user['uid'] && $user['role'] !== 'admin') {
                return back()->withErrors(['message' => 'Unauthorized deletion. You do not own this track.']);
            }

            // 2. Delete media from Cloudinary
            if (isset($song['audio_public_id'])) {
                $this->cloudinary->deleteFile($song['audio_public_id'], 'video');
            }
            if (isset($song['thumbnail_public_id'])) {
                $this->cloudinary->deleteFile($song['thumbnail_public_id'], 'image');
            }

            // 3. Delete document from Firestore
            $this->firebase->deleteDocument('songs', $songId);

            return redirect()->route('producer.dashboard')->with('success', 'Track deleted successfully!');

        } catch (Exception $e) {
            return back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }
}
