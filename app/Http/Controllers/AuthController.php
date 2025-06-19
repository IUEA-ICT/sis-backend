<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $firebaseFirestore;

    public function __construct()
    {
        try {
            $this->firebaseFirestore = app('firebase.firestore')->database();
        } catch (\Throwable $e) {
            Log::error('Firebase initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'reg_number' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        try {
            $this->firebaseFirestore->collection('reg_users')->document($request->reg_number)->set([
                'reg_number' => $request->reg_number,
                'password' => bcrypt($request->password),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->back()->with('success', 'Registration successful!');
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    private function getUsers()
    {
        try {
            $users = [];
            $documents = $this->firebaseFirestore->collection('reg_users')->documents();
            
            foreach ($documents as $document) {
                $users[] = $document->data();
            }
            
            return $users;
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return [];
        }
    }

    public function showRegistrationForm()
    {
        $users = $this->getUsers();
        return view('welcome', compact('users'));
    }
}
