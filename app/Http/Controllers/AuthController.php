<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Inertia\Inertia;
use Exception;

class AuthController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Show Login Page
     */
    public function showLogin()
    {
        if (session()->has('user')) {
            return redirect('/');
        }
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle Login Request
     */
    public function login(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            // 1. Verify token
            $decodedToken = $this->firebase->verifyIdToken($request->idToken);
            $uid = $decodedToken['sub'];
            $email = $decodedToken['email'] ?? '';

            // 2. Fetch User from Firestore
            $userDoc = $this->firebase->getDocument('users', $uid);

            if (!$userDoc) {
                // If user authenticated in Firebase but no Firestore profile exists (edge case)
                // We default them to a listener role
                $userDoc = $this->firebase->setDocument('users', $uid, [
                    'uid' => $uid,
                    'name' => $decodedToken['name'] ?? 'User',
                    'email' => $email,
                    'role' => 'user',
                    'created_at' => date('c'),
                ]);
            }

            // 3. Set Session
            session(['user' => [
                'uid' => $uid,
                'name' => $userDoc['name'] ?? 'User',
                'email' => $userDoc['email'] ?? $email,
                'role' => $userDoc['role'] ?? 'user',
            ]]);

            return redirect('/');

        } catch (Exception $e) {
            return back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show Register Page
     */
    public function showRegister()
    {
        if (session()->has('user')) {
            return redirect('/');
        }
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle Registration Request
     */
    public function register(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:user,producer',
        ]);

        try {
            // 1. Verify token
            $decodedToken = $this->firebase->verifyIdToken($request->idToken);
            $uid = $decodedToken['sub'];
            $email = $decodedToken['email'] ?? '';

            // 2. Sync to Firestore 'users' collection
            $userDoc = [
                'uid' => $uid,
                'name' => $request->name,
                'email' => $email,
                'role' => $request->role,
                'created_at' => date('c'),
            ];
            
            $this->firebase->setDocument('users', $uid, $userDoc);

            // 3. Set Session
            session(['user' => [
                'uid' => $uid,
                'name' => $request->name,
                'email' => $email,
                'role' => $request->role,
            ]]);

            return redirect('/');

        } catch (Exception $e) {
            return back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle Logout
     */
    public function logout()
    {
        session()->forget('user');
        return redirect('/login');
    }
}
