<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $request->user()->can('view users');

        $query = User::with('roles');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $role = $request->role;
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->user()->can('create users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Create user
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ];

            // Handle profile photo upload if provided
            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profile-photos', 'public');
                $userData['profile_photo_path'] = $path;
            }

            $user = User::create($userData);

            // Assign role
            $user->assignRole($validated['role']);

            return response()->json([
                'message' => 'User created successfully.',
                'user' => $user->load('roles')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user): JsonResponse
    {
        $request->user()->can('view users');

        return response()->json([
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $request->user()->can('edit users');

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => 'sometimes|required|exists:roles,name',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Update user data
            $updateData = [];
            
            if (isset($validated['name'])) {
                $updateData['name'] = $validated['name'];
            }
            
            if (isset($validated['email'])) {
                $updateData['email'] = $validated['email'];
            }
            
            if (isset($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Handle profile photo upload if provided
            if ($request->hasFile('profile_photo')) {
                // Delete old profile photo if exists
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $request->file('profile_photo')->store('profile-photos', 'public');
                $user->profile_photo_path = $path;
                $user->save();
            }

            // Update role if provided
            if (isset($validated['role'])) {
                $user->syncRoles([$validated['role']]);
            }

            return response()->json([
                'message' => 'User updated successfully.',
                'user' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        $request->user()->can('delete users');

        // Don't allow deletion of the current user or the last admin
        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.'
            ], 400);
        }

        // Check if this is the last admin
        $adminRole = Role::findByName('Admin');
        $admins = $adminRole->users()->count();

        if ($user->hasRole('Admin') && $admins <= 1) {
            return response()->json([
                'message' => 'Cannot delete the last admin user.'
            ], 400);
        }

        try {
            // Delete profile photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's profile photo.
     */
    public function updatePhoto(Request $request, User $user): JsonResponse
    {
        $request->user()->can('edit users');

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            // Delete old profile photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            // Store new profile photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();

            return response()->json([
                'message' => 'Profile photo updated successfully',
                'profile_photo_url' => $user->getProfilePhotoUrlAttribute(),
                'profile_photo_path' => $user->profile_photo_path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile photo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove user's profile photo.
     */
    public function removePhoto(Request $request, User $user): JsonResponse
    {
        $request->user()->can('edit users');

        try {
            // Delete old profile photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
                $user->profile_photo_path = null;
                $user->save();
            }

            return response()->json([
                'message' => 'Profile photo removed successfully',
                'profile_photo_url' => $user->getProfilePhotoUrlAttribute(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to remove profile photo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}