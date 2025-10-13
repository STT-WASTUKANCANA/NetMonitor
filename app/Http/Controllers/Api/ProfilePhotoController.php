<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    /**
     * Get the authenticated user's profile photo.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }
        
        return response()->json([
            'profile_photo_url' => $user->getProfilePhotoUrlAttribute(),
            'profile_photo_path' => $user->profile_photo_path,
        ]);
    }

    /**
     * Upload a new profile photo for the authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        // Check if user can update profile (should be allowed for authenticated users)
        if (!$user->can('update profile') && !$user->can('edit users')) {
            // If the user doesn't have explicit permission, check if they're updating their own profile
            // For basic functionality, we'll allow authenticated users to update their own profile
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Delete old profile photo if exists
        if ($user->profile_photo_path) {
            Storage::delete($user->profile_photo_path);
        }

        // Store new profile photo
        $path = $request->file('avatar')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
        $user->save();

        return response()->json([
            'message' => 'Profile photo updated successfully',
            'profile_photo_url' => $user->getProfilePhotoUrlAttribute(),
            'profile_photo_path' => $user->profile_photo_path,
        ]);
    }

    /**
     * Remove the authenticated user's profile photo.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();
        
        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        // Check if user can update profile (should be allowed for authenticated users)
        if (!$user->can('update profile') && !$user->can('edit users')) {
            // If the user doesn't have explicit permission, check if they're updating their own profile
            // For basic functionality, we'll allow authenticated users to update their own profile
        }

        // Delete old profile photo if exists
        if ($user->profile_photo_path) {
            // Delete the actual file from storage
            if (Storage::exists($user->profile_photo_path)) {
                Storage::delete($user->profile_photo_path);
            }
            
            $user->profile_photo_path = null;
            $user->save();
        }

        return response()->json([
            'message' => 'Profile photo deleted successfully',
            'profile_photo_url' => $user->getProfilePhotoUrlAttribute(),
        ]);
    }
}
