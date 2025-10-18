<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,petugas',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Handle profile photo upload if provided
        if ($request->hasFile('profile_photo')) {
            $request->validate([
                'profile_photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Don't validate password if not provided (for editing existing user)
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,petugas',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Handle profile photo upload if provided
        if ($request->hasFile('profile_photo')) {
            $request->validate([
                'profile_photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Delete old profile photo if exists
            if ($user->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::delete($user->profile_photo_path);
            }

            // Store new profile photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Don't allow deletion of the current user
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    
    /**
     * Update the specified user's profile photo.
     */
    public function updatePhoto(Request $request, User $user)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Delete old profile photo if exists
        if ($user->profile_photo_path) {
            \Illuminate\Support\Facades\Storage::delete($user->profile_photo_path);
        }

        // Store new profile photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
        $user->save();

        return redirect()->back()->with('success', 'Profile photo updated successfully.');
    }
    
    /**
     * Remove the specified user's profile photo.
     */
    public function removePhoto(Request $request, User $user)
    {
        // Delete old profile photo if exists
        if ($user->profile_photo_path) {
            \Illuminate\Support\Facades\Storage::delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();
        }

        return redirect()->back()->with('success', 'Profile photo removed successfully.');
    }
}