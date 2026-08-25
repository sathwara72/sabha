<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function index(): View
    {
        return view('pages.businesses');
    }

    public function show(int $id): View
    {
        $business = Business::where('status', 'approved')->find($id);

        if (! $business) {
            return view('pages.business-show', ['business' => null]);
        }

        $business->load(['user', 'businessCategory'])->loadAvg('reviews', 'rating')->loadCount('reviews');

        $services = collect($business->services ?? [])
            ->map(function ($service) {
                if (is_array($service)) {
                    $title = $service['title'] ?? '';

                    return ['title' => $title, 'desc' => $service['desc'] ?? ($title ? "Vetted capability in {$title}" : '')];
                }

                $title = (string) $service;

                return ['title' => $title, 'desc' => "Vetted capability in {$title}"];
            })
            ->filter(fn ($s) => $s['title'] !== '')
            ->values();

        $dbReviews = Review::where('business_id', $business->id)->with('user')->latest()->get();

        return view('pages.business-show', [
            'business' => $business,
            'services' => $services,
            'reviews' => $dbReviews,
            'hasUserReviewed' => auth()->check() && $dbReviews->contains(fn ($r) => $r->user_id === auth()->id()),
        ]);
    }

    public function storeReview(Request $request, Business $business): JsonResponse
    {
        $user = $request->user();

        if (Review::where('user_id', $user->id)->where('business_id', $business->id)->exists()) {
            return response()->json(['message' => 'You have already submitted a review for this business.'], 400);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:5',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'business_id' => $business->id,
            'rating' => $validated['rating'],
            'content' => $validated['content'],
        ]);

        $review->load('user');

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => [
                'reviewer' => $review->user->name,
                'role' => 'SABHA Member',
                'content' => $review->content,
                'rating' => $review->rating,
            ],
        ]);
    }
}
