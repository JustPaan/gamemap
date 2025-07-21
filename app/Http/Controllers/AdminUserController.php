<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organizer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Notifications\UserUpdatedNotification; 

class AdminUserController extends Controller
{
    // Show all users to admin
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.user2', compact('users'));
    }

    // Show form to add a new user
    public function create()
    {
        return view('admin.users.create'); // You'll create this view
    }

    // Store new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,organizer,gamer',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'notify'   => $request->has('notify'),
        ]);

        // In store method (after user creation)
        if ($request->role === 'organizer') {
            Organizer::create([
                'user_id' => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User added successfully!');
    }

    public function user2()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.user2', compact('users'));
    }

    // Bulk update (edit users and optionally delete)
    public function updateAll(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
            'users.*.name' => 'required|string|max:255',
            'users.*.email' => 'required|email|unique:users,email,'.$request->users[0]['id'],
            'users.*.phone' => 'nullable|string|max:20',
            'users.*.role' => 'required|in:admin,organizer,gamer',
        ]);

        try {
            DB::beginTransaction();
            
            foreach ($request->users as $userData) {
                $user = User::findOrFail($userData['id']);

                // Delete if marked (keeping delete functionality)
                if (isset($userData['delete']) && $userData['delete']) {
                    $user->delete();
                    continue;
                }

                // Update user with optimized data handling
                $updateData = [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'phone' => $userData['phone'] ?? null,
                    'role' => $userData['role'],
                    'notify' => isset($userData['notify']) ? true : false,
                ];

                $user->fill($updateData);
                
                if ($user->isDirty()) {
                    $user->save();
                }

                // In update method (after user update)
                if ($userData['role'] === 'organizer' && !$user->organizerProfile) {
                    Organizer::create([
                        'user_id' => $user->id,
                        'name'    => $user->name,
                        'email'   => $user->email,
                        'phone'   => $user->phone
                    ]);
                } elseif ($userData['role'] !== 'organizer' && $user->organizerProfile) {
                    $user->organizerProfile()->delete();
                }

                // Optional notification
                if ($userData['notify'] ?? false) {
                    $user->notify(new UserUpdatedNotification($updateData));
                }
            }
            
            DB::commit();
            
            // SUCCESS JSON RESPONSE (for AJAX)
            return response()->json([
                'success' => true,
                'message' => 'Users updated successfully!',
                'updated_count' => count($request->users)
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // ERROR JSON RESPONSE (for AJAX)
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString() // Remove in production
            ], 500);
        }
    }

    // Add this method to your AdminUserController
    public function destroy(User $user)
    {
        try {
            // Prevent deleting yourself
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'You cannot delete your own account!');
            }

            $user->delete();
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    public function promote(Request $request, User $user)
    {
        try {
            if ($user->role === 'organizer') {
                return response()->json(['success' => false, 'message' => 'User is already an organizer.']);
            }

            // Change user's role to organizer
            $user->role = 'organizer';
            $user->save();

            return response()->json(['success' => true, 'message' => 'User promoted to organizer successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error promoting user: ' . $e->getMessage()]);
        }
    }
}