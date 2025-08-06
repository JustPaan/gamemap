<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user(); // Get the authenticated user

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to edit your profile.');
        }

        $games = DB::table('games')->get();

        return view('profile.edit', [
            'user' => $user,
            'games' => $games,
            'selectedGames' => json_decode($user->games ?? '[]', true) ?? [],
            'selectedPlatforms' => json_decode($user->platforms ?? '[]', true) ?? []
        ]);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();
    $user = User::findOrFail($userId); // Use the User model

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$userId, // Validate email
        'bio' => 'nullable|string|max:500',
        'games' => 'nullable|array',
        'games.*' => 'string|exists:games,name',
        'platforms' => 'nullable|array',
        'platforms.*' => 'string|in:Console,PC,Mobile',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $validated = $validator->validated();
    $updateData = [
        'name' => $validated['name'],
        'email' => $validated['email'], // Save the email
        'bio' => $validated['bio'] ?? null,
        'games' => isset($validated['games']) ? json_encode($validated['games']) : null,
        'platforms' => isset($validated['platforms']) ? json_encode($validated['platforms']) : null,
        'updated_at' => now(),
    ];

    // Handle password update
    if (!empty($validated['password'])) {
        $updateData['password'] = Hash::make($validated['password']);
    }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            
            // Delete old avatar if it exists
            if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar)) {
                Storage::disk('public')->delete('avatars/' . $user->avatar);
            }
            
            // Generate a unique filename with timestamp
            $filename = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();
            
            // Store the file in the avatars directory
            $path = $avatar->storeAs('avatars', $filename, 'public');
            
            // Store only the filename in database
            $updateData['avatar'] = $filename;
        }

        // Update user data
        $user->update($updateData);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function info()
    {
        $user = Auth::user(); // Get the authenticated user
        return view('profile.info', compact('user')); // Pass the user data to the info view
    }
}