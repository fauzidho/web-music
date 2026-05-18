<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Services\CloudinaryService;
use Inertia\Inertia;
use Exception;

class AdminController extends Controller
{
    protected FirebaseService $firebase;
    protected CloudinaryService $cloudinary;

    public function __construct(FirebaseService $firebase, CloudinaryService $cloudinary)
    {
        $this->firebase = $firebase;
        $this->cloudinary = $cloudinary;
    }

    /**
     * Show Admin Dashboard
     */
    public function dashboard()
    {
        try {
            // 1. Fetch Users
            $users = $this->firebase->listDocuments('users');

            // 2. Fetch Songs
            $songs = $this->firebase->listDocuments('songs');

            return Inertia::render('Admin/Dashboard', [
                'users' => $users,
                'songs' => $songs
            ]);
        } catch (Exception $e) {
            return Inertia::render('Admin/Dashboard', [
                'users' => [],
                'songs' => [],
                'error' => 'Failed to load monitoring data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle User Role (Listener <=> Producer)
     */
    public function toggleRole($uid)
    {
        try {
            $user = $this->firebase->getDocument('users', $uid);

            if (!$user) {
                return back()->withErrors(['message' => 'User not found.']);
            }

            // Simple role switch
            $user['role'] = ($user['role'] === 'user') ? 'producer' : 'user';
            
            $this->firebase->setDocument('users', $uid, $user);

            return back()->with('success', 'User role updated successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Toggle Song Moderation Flag (Flag <=> Unflag)
     */
    public function toggleModeration($songId)
    {
        try {
            $song = $this->firebase->getDocument('songs', $songId);

            if (!$song) {
                return back()->withErrors(['message' => 'Track not found.']);
            }

            $song['is_moderated'] = !($song['is_moderated'] ?? false);
            
            $this->firebase->setDocument('songs', $songId, $song);

            return back()->with('success', 'Track moderation status updated.');
        } catch (Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Admin Force Delete Track
     */
    public function deleteSong($songId)
    {
        try {
            $song = $this->firebase->getDocument('songs', $songId);

            if (!$song) {
                return back()->withErrors(['message' => 'Track not found.']);
            }

            // Delete media from Cloudinary
            if (isset($song['audio_public_id'])) {
                $this->cloudinary->deleteFile($song['audio_public_id'], 'video');
            }
            if (isset($song['thumbnail_public_id'])) {
                $this->cloudinary->deleteFile($song['thumbnail_public_id'], 'image');
            }

            // Delete document from Firestore
            $this->firebase->deleteDocument('songs', $songId);

            return back()->with('success', 'Track permanently moderated and deleted.');
        } catch (Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
