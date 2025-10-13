<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Update a specific user's profile photo.
     */
    public function updatePhoto(Request $request, User $user): JsonResponse
    {
        // Authorize the action - users can update their own photo or admins can update any
        $currentUser = $request->user();
        if (!$currentUser || (!$currentUser->can('edit users') && $currentUser->id !== $user->id)) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Delete old profile photo if exists
        if ($user->profile_photo_path) {
            // Delete the actual file from storage
            if (Storage::exists($user->profile_photo_path)) {
                Storage::delete($user->profile_photo_path);
            }
        }

        // Store new profile photo
        $path = $request->file('avatar')->store('profile-photos', 'public');
        $user->profile_photo_path = $path;
        $user->save();

        return response()->json([
            'message' => 'User profile photo updated successfully',
            'profile_photo_url' => $user->getProfilePhotoUrlAttribute(),
            'profile_photo_path' => $user->profile_photo_path,
        ]);
    }

    /**
     * Remove a specific user's profile photo.
     */
    public function removePhoto(Request $request, User $user): JsonResponse
    {
        // Authorize the action - users can remove their own photo or admins can remove any
        $currentUser = $request->user();
        if (!$currentUser || (!$currentUser->can('edit users') && $currentUser->id !== $user->id)) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 403);
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
            'message' => 'User profile photo removed successfully',
            'profile_photo_url' => $user->getProfilePhotoUrlAttribute(),
        ]);
    }
}