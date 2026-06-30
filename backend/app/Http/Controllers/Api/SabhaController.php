<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Event;
use App\Models\Statistic;
use App\Models\GalleryImage;
use App\Models\User;
use App\Models\Setting;
use App\Models\Review;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SabhaController extends Controller
{
    public function getBusinesses()
    {
        // Only return approved businesses for the public frontend
        return response()->json(Business::where('status', 'approved')->with('user')->get());
    }

    public function getAllBusinesses()
    {
        // Admin view: return all businesses
        return response()->json(Business::with('user')->latest()->get());
    }

    public function getEvents()
    {
        return response()->json(Event::with('galleryImages')->get());
    }

    public function getStatistics()
    {
        return response()->json(Statistic::all());
    }

    public function approveBusiness($id)
    {
        $business = Business::findOrFail($id);
        $business->update([
            'status' => 'approved', 
            'is_verified' => true,
            'rejection_reason' => null
        ]);
        return response()->json(['message' => 'Business approved successfully', 'business' => $business]);
    }

    public function rejectBusiness(Request $request, $id)
    {
        $business = Business::findOrFail($id);
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);
        $business->update([
            'status' => 'rejected', 
            'is_verified' => false,
            'rejection_reason' => $validated['rejection_reason']
        ]);
        return response()->json(['message' => 'Business rejected successfully', 'business' => $business]);
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string',
            'google_map_link' => 'nullable|string',
            'type' => 'required|string',
            'price_normal' => 'required|string',
            'price_verified' => 'required|string',
        ]);

        $event = Event::create($validated);
        return response()->json(['message' => 'Event created successfully', 'event' => $event]);
    }

    public function getUsers()
    {
        return response()->json(User::with('business')->get());
    }

    public function registerSendOtp(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $otp = (string) mt_rand(100000, 999999);

        Cache::put('reg_otp_' . $validated['email'], [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'otp' => $otp
        ], 900);

        Log::info("SABHA Registration OTP for {$validated['email']}: {$otp}");

        return response()->json([
            'message' => 'Email verification code has been sent to your email.',
            'email' => $validated['email'],
            'otp' => $otp
        ]);
    }

    public function registerConfirm(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
        ]);

        $cached = Cache::get('reg_otp_' . $validated['email']);

        if (!$cached || $cached['otp'] !== $validated['otp']) {
            return response()->json(['message' => 'Invalid or expired OTP verification code.'], 400);
        }

        $user = User::create([
            'name' => $cached['name'],
            'email' => $cached['email'],
            'password' => $cached['password'],
            'role' => 'user',
        ]);

        Cache::forget('reg_otp_' . $validated['email']);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified and account registered successfully.',
            'user' => $user,
            'token' => $token
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'We could not find a user with that email address.'], 404);
        }

        $resetCode = (string) mt_rand(100000, 999999);
        Log::info("SABHA Password Reset OTP for {$validated['email']}: {$resetCode}");

        $user->password = Hash::make('password123');
        $user->save();

        return response()->json([
            'message' => 'We have sent password reset instructions to your email. For simulation, your password has been reset to "password123". Please log in and change it in your profile.',
            'reset_code' => $resetCode
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Instant dummy/demo login — creates (or reuses) a temporary throwaway
     * account so the app can be explored without real credentials or OTP.
     */
    public function demoLogin(Request $request)
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@sabha.test'],
            [
                'name' => 'Demo Member',
                'password' => Hash::make('demo1234'),
                'role' => 'user',
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in with demo account',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function submitBusiness(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category' => 'required|string',
            'tagline' => 'nullable|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'linkedin' => 'nullable|string',
            'instagram' => 'nullable|string',
            'youtube' => 'nullable|string',
            'twitter' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'hours' => 'nullable|string',
            'founded' => 'nullable|string',
            'team_size' => 'nullable|string',
            'projects' => 'nullable|string',
            'services' => 'nullable',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'payment_screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $screenshotPath = null;
        if ($request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $fileName = time() . '_payment_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/payments', $fileName);
            $screenshotPath = '/storage/payments/' . $fileName;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_logo_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/logos', $fileName);
            $logoPath = '/storage/logos/' . $fileName;
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $fileName = time() . '_cover_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/covers', $fileName);
            $coverPath = '/storage/covers/' . $fileName;
        }

        $userId = $request->user() ? $request->user()->id : null;

        $servicesInput = $request->input('services');
        if (is_string($servicesInput)) {
            $servicesArray = json_decode($servicesInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If it is not valid JSON, treat as comma-separated or plain text wrapped in array
                $servicesArray = array_map('trim', explode(',', $servicesInput));
            }
        } else {
            $servicesArray = $servicesInput;
        }

        $businessData = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'tagline' => $validated['tagline'] ?? null,
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'website' => $validated['website'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'youtube' => $validated['youtube'] ?? null,
            'twitter' => $validated['twitter'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'hours' => $validated['hours'] ?? null,
            'founded' => $validated['founded'] ?? null,
            'team_size' => $validated['team_size'] ?? null,
            'projects' => $validated['projects'] ?? null,
            'services' => $servicesArray,
            'status' => 'pending',
            'is_verified' => false,
            'rejection_reason' => null,
        ];

        if ($screenshotPath) {
            $businessData['payment_screenshot'] = $screenshotPath;
        }
        if ($logoPath) {
            $businessData['logo'] = $logoPath;
        }
        if ($coverPath) {
            $businessData['cover_image'] = $coverPath;
        }

        if ($userId) {
            $existing = Business::where('user_id', $userId)->first();
            if ($existing) {
                if ($existing->status === 'pending') {
                    return response()->json(['message' => 'Your business profile is already in progress. Please wait for approval.'], 400);
                }
                if ($existing->status === 'approved') {
                    $businessData['status'] = 'approved';
                    $businessData['is_verified'] = true;
                }
            }

            $businessData['user_id'] = $userId;

            if ($existing) {
                $existing->update($businessData);
                $business = $existing;
            } else {
                if (!$screenshotPath) {
                    return response()->json(['message' => 'Payment screenshot is required for new submissions.'], 400);
                }
                $business = Business::create($businessData);
            }
        } else {
            if (!$screenshotPath) {
                return response()->json(['message' => 'Payment screenshot is required for new submissions.'], 400);
            }
            $business = Business::create($businessData);
        }

        return response()->json([
            'message' => 'Business submitted successfully and is pending approval',
            'business' => $business
        ]);
    }

    public function getUserBusiness(Request $request)
    {
        $user = $request->user();
        $business = Business::where('user_id', $user->id)->with('user')->first();
        return response()->json($business);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string',
            'city' => 'nullable|string',
            'designation' => 'nullable|string',
            'company' => 'nullable|string',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->phone = $validated['phone'] ?? null;
        $user->city = $validated['city'] ?? null;
        $user->designation = $validated['designation'] ?? null;
        $user->company = $validated['company'] ?? null;
        $user->bio = $validated['bio'] ?? null;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/avatars', $fileName);
            $user->avatar = '/storage/avatars/' . $fileName;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    public function getGalleryImages()
    {
        $images = GalleryImage::latest()->get();
        return response()->json($images);
    }

    public function uploadGalleryImage(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'nullable|integer',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm,mkv|max:51200', // support up to 50MB
            'caption' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/gallery', $fileName);
            $imagePath = '/storage/gallery/' . $fileName;
        } else {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        $galleryImage = GalleryImage::create([
            'event_id' => $validated['event_id'] ?? null,
            'image_path' => $imagePath,
            'caption' => $validated['caption'] ?? null,
        ]);

        return response()->json([
            'message' => 'Gallery media uploaded successfully',
            'gallery_image' => $galleryImage
        ]);
    }

    public function updateStatistic(Request $request, $id)
    {
        $statistic = Statistic::findOrFail($id);
        $validated = $request->validate([
            'value' => 'required|string',
            'label' => 'required|string',
        ]);
        $statistic->update($validated);
        return response()->json(['message' => 'Statistic updated successfully', 'statistic' => $statistic]);
    }

    public function getSettings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }

        $settings = Setting::all()->pluck('value', 'key');
        return response()->json(['message' => 'Settings updated successfully', 'settings' => $settings]);
    }

    public function getReviews($id)
    {
        $reviews = Review::where('business_id', $id)->with('user')->latest()->get();
        return response()->json($reviews);
    }

    public function submitReview(Request $request, $id)
    {
        $user = $request->user();
        
        // Enforce the one-review-per-member-per-business rule
        $exists = Review::where('user_id', $user->id)->where('business_id', $id)->exists();
        if ($exists) {
            return response()->json(['message' => 'You have already submitted a review for this business.'], 400);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:5',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'business_id' => $id,
            'rating' => $validated['rating'],
            'content' => $validated['content'],
        ]);

        // Fetch user relationship to return with the review
        $review->load('user');

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review
        ]);
    }

    public function reserveEventSpot(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $user = $request->user();

        $existing = EventRegistration::where('event_id', $id)->where('user_id', $user->id)->first();
        if ($existing) {
            return response()->json([
                'message' => 'You have already requested a reservation for this event.',
                'registration' => $existing
            ], 400);
        }

        $validated = $request->validate([
            'ticket_type' => 'required|string|in:standard,verified',
            'amount_paid' => 'required|numeric',
            'payment_screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $screenshotPath = null;
        if ($request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $fileName = time() . '_ticket_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/event_payments', $fileName);
            $screenshotPath = '/storage/event_payments/' . $fileName;
        }

        // Generate custom ticket number
        $ticketNo = 'SABHA-' . date('Y') . '-' . mt_rand(1000, 9999) . '-' . mt_rand(10, 99);

        $registration = EventRegistration::create([
            'event_id' => $id,
            'user_id' => $user->id,
            'ticket_number' => $ticketNo,
            'status' => 'pending',
            'payment_screenshot' => $screenshotPath,
            'ticket_type' => $validated['ticket_type'],
            'amount_paid' => $validated['amount_paid'],
        ]);

        return response()->json([
            'message' => 'Reservation request submitted successfully and is pending verification',
            'registration' => $registration
        ]);
    }

    public function getUserRegistrations(Request $request)
    {
        $user = $request->user();
        $registrations = EventRegistration::where('user_id', $user->id)->with('event')->latest()->get();
        return response()->json($registrations);
    }

    public function getAllEventRegistrations()
    {
        $registrations = EventRegistration::with(['user', 'event'])->latest()->get();
        return response()->json($registrations);
    }

    public function approveEventRegistration($id)
    {
        $registration = EventRegistration::findOrFail($id);
        $registration->update([
            'status' => 'approved',
            'rejection_reason' => null
        ]);
        return response()->json([
            'message' => 'Event registration approved successfully',
            'registration' => $registration
        ]);
    }

    public function rejectEventRegistration(Request $request, $id)
    {
        $registration = EventRegistration::findOrFail($id);
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason']
        ]);

        return response()->json([
            'message' => 'Event registration rejected successfully',
            'registration' => $registration
        ]);
    }

    public function uploadEventPhotos(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $user = $request->user();

        // Check if event is in the past
        if ($event->date > now()) {
            return response()->json(['message' => 'This event has not finished yet.'], 400);
        }

        // Verify that the user has an approved registration for this event
        $reg = EventRegistration::where('event_id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if (!$reg) {
            return response()->json(['message' => 'You must be an approved attendee of this event to upload photos.'], 403);
        }

        $validated = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'caption' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/gallery', $fileName);
            $imagePath = '/storage/gallery/' . $fileName;
        } else {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        $galleryImage = GalleryImage::create([
            'event_id' => $id,
            'image_path' => $imagePath,
            'caption' => $validated['caption'] ?? null,
        ]);

        return response()->json([
            'message' => 'Event media uploaded successfully',
            'gallery_image' => $galleryImage
        ]);
    }
}
