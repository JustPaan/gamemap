<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewOrganizerNotification;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

public function register(Request $request)
{
    $this->validator($request->all())->validate();

    event(new Registered($user = $this->create($request->all())));

    // Notify admin if user is an organizer
    if ($user->role === 'organizer') {
        $this->notifyAdminAboutNewOrganizer($user);
        return redirect()->route('login')->with('success', 'Registration successful! Your account is pending admin approval.');
    }

    // For normal users (gamers), redirect to login
    return redirect()->route('login')->with('success', 'Registration successful! You can now log in.');
}

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'birthday' => ['required', 'date', 'before:-13 years'],
            'role' => ['required', 'in:gamer,organizer'],
        ]);
    }

    protected function create(array $data)
    {
        $isOrganizer = isset($data['role']) && $data['role'] === 'organizer';

        $user = User::create([
            'name' => $data['name'],
            'nickname' => $data['nickname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'birthday' => $data['birthday'],
            'role' => $data['role'],
            'is_approved' => $isOrganizer ? null : true,
            'requested_organizer' => $isOrganizer,
        ]);

        $user->assignRole($data['role']);

        return $user;
    }
    

    protected function redirectPath()
    {
        return route('home');
    }

    protected function notifyAdminAboutNewOrganizer(User $organizer)
    {
        // Get all admin users
        $admins = User::role('admin')->get();
        
        // Send notification to each admin
        foreach ($admins as $admin) {
            $admin->notify(new NewOrganizerNotification($organizer));
        }
    }
}