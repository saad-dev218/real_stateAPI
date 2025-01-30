<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Exception;

class ListingController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $listings = Auth::user()->listings;

            return $this->sendSuccess(
                $listings->isEmpty() ? 'No listings found' : 'Listings fetched successfully',
                $listings->toArray()
            );
        } catch (Exception $e) {
            return $this->sendError('Error fetching listings', ['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $listing = Auth::user()->listings()->create($validatedData);

            return $this->sendSuccess('Listing created successfully', $listing->toArray(), 201);
        } catch (Exception $e) {
            return $this->sendError('Error creating listing', ['error' => $e->getMessage()], 500);
        }
    }

    public function show(Listing $listing)
    {
        try {
            if ($listing->user_id !== Auth::id()) {
                return $this->sendError('Unauthorized access', [], 403);
            }

            return $this->sendSuccess('Listing details fetched successfully', $listing->toArray());
        } catch (Exception $e) {
            return $this->sendError('Error fetching listing details', ['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Listing $listing)
    {
        try {
            if ($listing->user_id !== Auth::id()) {
                return $this->sendError('Unauthorized action', [], 403);
            }

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            $listing->update($validatedData);

            return $this->sendSuccess('Listing updated successfully', $listing->toArray());
        } catch (Exception $e) {
            return $this->sendError('Error updating listing', ['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Listing $listing)
    {
        try {
            if ($listing->user_id !== Auth::id()) {
                return $this->sendError('Unauthorized action', [], 403);
            }

            $listing->delete();

            return $this->sendSuccess('Listing deleted successfully');
        } catch (Exception $e) {
            return $this->sendError('Error deleting listing', ['error' => $e->getMessage()], 500);
        }
    }

    public function featuredListings()
    {
        try {
            $listings = Listing::where('featured', true)->get();

            return $this->sendSuccess('Featured listings fetched successfully', $listings->toArray());
        } catch (Exception $e) {
            return $this->sendError('Error fetching featured listings', ['error' => $e->getMessage()], 500);
        }
    }
}
