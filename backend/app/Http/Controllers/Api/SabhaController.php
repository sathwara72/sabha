<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Event;
use App\Models\Statistic;
use Illuminate\Http\Request;

class SabhaController extends Controller
{
    public function getBusinesses()
    {
        // Only return approved businesses for the public frontend
        return response()->json(Business::where('status', 'approved')->get());
    }

    public function getAllBusinesses()
    {
        // Admin view: return all businesses
        return response()->json(Business::latest()->get());
    }

    public function getEvents()
    {
        return response()->json(Event::all());
    }

    public function getStatistics()
    {
        return response()->json(Statistic::all());
    }

    public function approveBusiness($id)
    {
        $business = Business::findOrFail($id);
        $business->update(['status' => 'approved', 'is_verified' => true]);
        return response()->json(['message' => 'Business approved successfully', 'business' => $business]);
    }

    public function rejectBusiness($id)
    {
        $business = Business::findOrFail($id);
        $business->update(['status' => 'rejected', 'is_verified' => false]);
        return response()->json(['message' => 'Business rejected successfully', 'business' => $business]);
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string',
            'type' => 'required|string',
        ]);

        $event = Event::create($validated);
        return response()->json(['message' => 'Event created successfully', 'event' => $event]);
    }

    public function getUsers()
    {
        return response()->json(\App\Models\User::all());
    }
}
