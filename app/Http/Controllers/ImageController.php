<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Listing;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    use ResponseTrait;

    public function storeListingImages(Request $request, Listing $listing)
    {
        try {
            $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'image|max:2048', // Validation for each image
            ]);

            if ($listing->user_id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $images = [];

            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('images', 'public');

                $image = Image::create([
                    'path' => $path,
                    'imageable_id' => $listing->id,
                    'imageable_type' => Listing::class,
                ]);

                $images[] = $image;
            }

            return $this->sendSuccess('Images uploaded successfully', $images, 201);
        } catch (Exception $e) {
            \Log::error('Error uploading images: ' . $e->getMessage());
            return $this->sendError('Error uploading images', ['error' => $e->getMessage()], 500);
        }
    }

    public function storeUserProfileImage(Request $request, User $user)
    {
        try {
            $request->validate([
                'image' => 'required|image|max:2048',
            ]);

            if ($user->id !== Auth::id()) {
                return $this->sendError('Unauthorized', [], 403);
            }

            $path = $request->file('image')->store('profile_images', 'public');

            $image = Image::create([
                'path' => $path,
                'imageable_id' => $user->id,
                'imageable_type' => User::class,
            ]);

            return $this->sendSuccess('Profile image uploaded successfully', $image, 201);
        } catch (Exception $e) {
            \Log::error('Error uploading profile image: ' . $e->getMessage());
            return $this->sendError('Error uploading profile image', ['error' => $e->getMessage()], 500);
        }
    }
}
