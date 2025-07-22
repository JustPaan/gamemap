<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User; // Using User model since organizers are stored in users table

class OrganizerProfileController extends Controller
{
    public function index()
    {
        $organizer = Auth::user();

        if (!$organizer) {
            return redirect()->route('login')->with('error', 'You must be logged in to view your profile.');
        }

        return view('organizer.profile', compact('organizer'));
    }

    public function edit()
    {
        $organizer = Auth::user();

        if (!$organizer) {
            return redirect()->route('login')->with('error', 'You must be logged in to edit your profile.');
        }

        return view('organizer.edit', [
            'organizer' => $organizer,
        ]);
    }

public function update(Request $request)
{
    $userId = Auth::id();
    $user = User::findOrFail($userId);

    $validator = Validator::make($request->all(), [
        'phone' => 'nullable|string|max:20',
        'bio' => 'nullable|string|max:500',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $updateData = [
        'phone' => $request->input('phone'),
        'bio' => $request->input('bio'),
    ];

    // Handle avatar upload
    if ($request->hasFile('avatar')) {
        $avatar = $request->file('avatar');
        $filename = 'avatar_'.$userId.'_'.time().'.'.$avatar->getClientOriginalExtension();
        
        // Ensure the avatars directory exists
        $avatarsDir = storage_path('app/public/avatars');
        if (!file_exists($avatarsDir)) {
            mkdir($avatarsDir, 0755, true);
        }
        
        // Store the file
        $path = $avatar->storeAs('avatars', $filename, 'public');
        
        // Delete old avatar if it exists
        if ($user->avatar) {
            $oldAvatarPath = storage_path('app/public/' . $user->avatar);
            if (file_exists($oldAvatarPath)) {
                unlink($oldAvatarPath);
            }
        }
        
        $updateData['avatar'] = $path;
    }

    // Update user data
    $user->update($updateData);

    return redirect()->route('organizer.profile') // Changed from edit to profile
        ->with('success', 'Profile updated successfully!')
        ->withInput(); // Keep form data on redirect
}

    public function info()
    {
        $organizer = Auth::user();
        return view('organizer.info', compact('organizer'));
    }
}