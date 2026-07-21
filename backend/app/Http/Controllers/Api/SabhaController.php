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
use App\Models\HeroImage;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class SabhaController extends Controller
{
    public function getBusinesses()
    {
        // Only return approved businesses for the public frontend
        return response()->json(
            Business::where('status', 'approved')
                ->with(['user', 'businessCategory'])
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->get()
        );
    }

    public function getAllBusinesses()
    {
        // Admin view: return all businesses
        return response()->json(
            Business::with(['user', 'businessCategory'])
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->latest()
                ->get()
        );
    }

    public function getEvents()
    {
        return response()->json(Event::with(['galleryImages', 'approvedRegistrations.user'])->get());
    }

    public function getStatistics()
    {
        try {
            // Count registered users
            $userCount = User::count();
            if ($userCount > 0) {
                Statistic::where('label', 'like', '%Professional%')
                    ->orWhere('label', 'like', '%Member%')
                    ->update(['value' => $userCount . '+']);
            }

            // Count events
            $eventCount = Event::count();
            if ($eventCount > 0) {
                Statistic::where('label', 'like', '%Event%')
                    ->update(['value' => $eventCount . '+']);
                Statistic::where('label', 'like', '%Mixer%')
                    ->update(['value' => $eventCount . '+']);
            }

            // Count unique business locations/cities
            $cityCount = Business::whereNotNull('location')
                ->where('location', '!=', '')
                ->distinct('location')
                ->count('location');
            
            if ($cityCount > 0) {
                Statistic::where('label', 'like', '%Cit%')
                    ->update(['value' => $cityCount . '+']);
            }
        } catch (\Exception $e) {
            Log::error("Failed to dynamically update statistics: " . $e->getMessage());
        }

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
        $agenda = $request->input('agenda');
        if (is_string($agenda)) {
            $decoded = json_decode($agenda, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['agenda' => $decoded]);
            }
        }

        $speakers = $request->input('speakers');
        if (is_string($speakers)) {
            $decoded = json_decode($speakers, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['speakers' => $decoded]);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string',
            'google_map_link' => 'nullable|string',
            'type' => 'required|string',
            'price_normal' => 'required|string',
            'price_verified' => 'required|string',
            'agenda' => 'nullable|array',
            'speakers' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        // Automatically generate event code
        $cleanTitle = preg_replace('/[^a-zA-Z0-9\s]/', '', $validated['title']);
        $words = explode(' ', trim($cleanTitle));
        $code = '';
        if (count($words) >= 2) {
            foreach ($words as $word) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        } else {
            $code = strtoupper(substr($cleanTitle, 0, 4));
        }
        $code = preg_replace('/[^A-Z0-9]/', '', $code);
        if (strlen($code) < 3) {
            $code .= mt_rand(100, 999);
        }
        $eventCode = substr($code, 0, 6);

        // Ensure uniqueness of event_code
        $originalCode = $eventCode;
        $counter = 1;
        while (Event::where('event_code', $eventCode)->exists()) {
            $eventCode = substr($originalCode, 0, 4) . $counter;
            $counter++;
        }

        $validated['event_code'] = $eventCode;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_event_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('events', $fileName, 'public');
            $validated['image'] = '/storage/events/' . $fileName;
        }

        $event = Event::create($validated);
        return response()->json(['message' => 'Event created successfully', 'event' => $event]);
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $agenda = $request->input('agenda');
        if (is_string($agenda)) {
            $decoded = json_decode($agenda, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['agenda' => $decoded]);
            }
        }

        $speakers = $request->input('speakers');
        if (is_string($speakers)) {
            $decoded = json_decode($speakers, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['speakers' => $decoded]);
            }
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'required|string',
            'google_map_link' => 'nullable|string',
            'type' => 'required|string',
            'price_normal' => 'required|string',
            'price_verified' => 'required|string',
            'agenda' => 'nullable|array',
            'speakers' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_event_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('events', $fileName, 'public');
            $validated['image'] = '/storage/events/' . $fileName;
        }

        $event->update($validated);

        return response()->json(['message' => 'Event updated successfully', 'event' => $event]);
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

        // Send OTP email
        try {
            Mail::to($validated['email'])->send(new OtpMail($otp, $validated['name']));
            Log::info("SABHA Registration OTP email sent to {$validated['email']}");
        } catch (\Exception $e) {
            Log::error("SABHA OTP email failed for {$validated['email']}: " . $e->getMessage());
            return response()->json([
                'message' => 'Could not send verification email. Please check your email address and try again.',
            ], 500);
        }

        return response()->json([
            'message' => 'Email verification code has been sent to your email.',
            'email' => $validated['email'],
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
            'name' => 'nullable|string',
            'category' => 'nullable|string',
            'tagline' => 'nullable|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'website' => 'nullable|string',
            'phone' => 'nullable|string|max:10',
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
            $file->storeAs('payments', $fileName, 'public');
            $screenshotPath = '/storage/payments/' . $fileName;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_logo_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('logos', $fileName, 'public');
            $logoPath = '/storage/logos/' . $fileName;
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $fileName = time() . '_cover_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('covers', $fileName, 'public');
            $coverPath = '/storage/covers/' . $fileName;
        }

        $user = $request->user();
        $userId = $user ? $user->id : null;
        
        $name = ($validated['name'] ?? null) ?: ('Business of ' . ($user ? $user->name : 'User'));
        $category = ($validated['category'] ?? null) ?: 'Software Development';

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
            'name' => $name,
            'category' => $category,
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
        if (!$business) {
            return response()->json(null, 404);
        }
        return response()->json($business);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:10',
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
            $file->storeAs('avatars', $fileName, 'public');
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
        // Support both single file 'image' and multiple files array 'images' or 'images[]'
        $files = [];
        if ($request->hasFile('images')) {
            $uploaded = $request->file('images');
            $files = is_array($uploaded) ? $uploaded : [$uploaded];
        } elseif ($request->hasFile('image')) {
            $files = [$request->file('image')];
        }

        if (empty($files)) {
            return response()->json(['message' => 'No files uploaded'], 400);
        }

        $createdImages = [];

        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'zip') {
                if (!class_exists('\ZipArchive')) {
                    return response()->json([
                        'message' => 'ZIP extraction is not supported on the server because the PHP "zip" extension (php-zip) is not enabled on hosting.'
                    ], 400);
                }

                $zip = new \ZipArchive();
                if ($zip->open($file->getRealPath()) === true) {
                    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'avi', 'webm', 'mkv'];
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        if (str_contains($filename, '__MACOSX') || str_starts_with(basename($filename), '.') || str_ends_with($filename, '/')) {
                            continue;
                        }
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        if (in_array($ext, $allowedExts)) {
                            $fileStream = $zip->getStream($filename);
                            if ($fileStream) {
                                $newFileName = time() . '_' . uniqid() . '.' . $ext;
                                $destinationDir = public_path('storage/gallery');
                                if (!file_exists($destinationDir)) {
                                    mkdir($destinationDir, 0755, true);
                                }

                                $destFilePath = $destinationDir . '/' . $newFileName;
                                $tempZipFilePath = sys_get_temp_dir() . '/' . uniqid('zip_img_') . '.' . $ext;

                                file_put_contents($tempZipFilePath, stream_get_contents($fileStream));
                                fclose($fileStream);

                                $this->compressAndSaveImage($tempZipFilePath, $destFilePath, $ext);
                                @unlink($tempZipFilePath);

                                $imagePath = '/storage/gallery/' . $newFileName;
                                $galleryImage = GalleryImage::create([
                                    'event_id' => $request->input('event_id'),
                                    'image_path' => $imagePath,
                                    'caption' => $request->input('caption'),
                                ]);
                                $createdImages[] = $galleryImage;
                            }
                        }
                    }
                    $zip->close();
                } else {
                    return response()->json([
                        'message' => 'Failed to open or extract the ZIP file. Please ensure it is a valid zip archive.'
                    ], 400);
                }
            } else {
                $newFileName = time() . '_' . uniqid() . '.' . $extension;
                $destinationDir = public_path('storage/gallery');
                if (!file_exists($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }
                $destFilePath = $destinationDir . '/' . $newFileName;

                $this->compressAndSaveImage($file->getRealPath(), $destFilePath, $extension);

                $imagePath = '/storage/gallery/' . $newFileName;

                $galleryImage = GalleryImage::create([
                    'event_id' => $request->input('event_id'),
                    'image_path' => $imagePath,
                    'caption' => $request->input('caption'),
                ]);
                $createdImages[] = $galleryImage;
            }
        }

        $count = count($createdImages);
        if ($count === 0) {
            return response()->json(['message' => 'No valid media files were uploaded.'], 400);
        }

        return response()->json([
            'message' => "Successfully uploaded {$count} media items!",
            'gallery_images' => $createdImages
        ]);
    }

    public function deleteGalleryImage($id)
    {
        $galleryImage = GalleryImage::findOrFail($id);
        
        $path = public_path($galleryImage->image_path);
        if (file_exists($path)) {
            @unlink($path);
        }

        $galleryImage->delete();

        return response()->json(['message' => 'Gallery media deleted successfully']);
    }

    private function compressAndSaveImage($sourcePath, $destPath, $ext)
    {
        $ext = strtolower($ext);
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            copy($sourcePath, $destPath);
            return;
        }

        if (!function_exists('imagecreatefromstring')) {
            copy($sourcePath, $destPath);
            return;
        }

        try {
            $content = file_get_contents($sourcePath);
            if (!$content) {
                copy($sourcePath, $destPath);
                return;
            }

            $srcImage = @imagecreatefromstring($content);
            if (!$srcImage) {
                copy($sourcePath, $destPath);
                return;
            }

            $width = imagesx($srcImage);
            $height = imagesy($srcImage);

            // Maximum bounds (Full HD 1920px)
            $maxDimension = 1920;
            if ($width > $maxDimension || $height > $maxDimension) {
                if ($width >= $height) {
                    $newWidth = $maxDimension;
                    $newHeight = (int) round(($height / $width) * $maxDimension);
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = (int) round(($width / $height) * $maxDimension);
                }

                $dstImage = imagecreatetruecolor($newWidth, $newHeight);

                if ($ext === 'png' || $ext === 'webp') {
                    imagealphablending($dstImage, false);
                    imagesavealpha($dstImage, true);
                }

                imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($srcImage);
                $srcImage = $dstImage;
            }

            // Save compressed (75% quality for JPEG/WebP, 7/9 for PNG)
            if ($ext === 'png') {
                imagepng($srcImage, $destPath, 7);
            } elseif ($ext === 'webp') {
                imagewebp($srcImage, $destPath, 75);
            } else {
                imagejpeg($srcImage, $destPath, 75);
            }

            imagedestroy($srcImage);
        } catch (\Throwable $e) {
            copy($sourcePath, $destPath);
        }
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
            $file->storeAs('event_payments', $fileName, 'public');
            $screenshotPath = '/storage/event_payments/' . $fileName;
        }

        $registration = EventRegistration::create([
            'event_id' => $id,
            'user_id' => $user->id,
            'ticket_number' => null,
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
        $registration = EventRegistration::with(['user', 'event'])->findOrFail($id);
        $event = $registration->event;

        // Generate event code if not present
        $eventCode = $event->event_code;
        if (!$eventCode) {
            $cleanTitle = preg_replace('/[^a-zA-Z0-9\s]/', '', $event->title);
            $words = explode(' ', trim($cleanTitle));
            $code = '';
            if (count($words) >= 2) {
                foreach ($words as $word) {
                    $code .= strtoupper(substr($word, 0, 1));
                }
            } else {
                $code = strtoupper(substr($cleanTitle, 0, 4));
            }
            $code = preg_replace('/[^A-Z0-9]/', '', $code);
            if (strlen($code) < 3) {
                $code .= mt_rand(100, 999);
            }
            $eventCode = substr($code, 0, 6);
            $event->update(['event_code' => $eventCode]);
        }

        // Get the year from event date or current year
        $year = $event->date ? $event->date->format('Y') : date('Y');

        // Generate custom unique ticket number: {year}-{eventcode}-{number}
        $ticketNo = $registration->ticket_number;
        if (!$ticketNo) {
            do {
                $ticketNo = $year . '-' . $eventCode . '-' . mt_rand(1000, 9999);
            } while (EventRegistration::where('ticket_number', $ticketNo)->exists());
        }

        $registration->update([
            'status' => 'approved',
            'ticket_number' => $ticketNo,
            'rejection_reason' => null
        ]);

        // Generate QR code and send email to the user
        try {
            $userEmail = $registration->user->email;
            $userName = $registration->user->name;
            $eventName = $event->title;

            // Generate QR code image content inline
            $qrCode = new \App\Utils\QRCode($ticketNo, [
                's' => 'qrm', // Medium ECC
                'sf' => 8,    // Scale factor
                'p' => 2,     // Padding
            ]);
            $image = $qrCode->render_image();
            ob_start();
            imagepng($image);
            $imageData = ob_get_clean();
            imagedestroy($image);

            Mail::send([], [], function ($message) use ($userEmail, $userName, $eventName, $ticketNo, $imageData) {
                // Embed raw binary image data into the email
                $qrCodeCid = $message->embedData($imageData, 'qrcode.png', 'image/png');

                $message->to($userEmail)
                    ->subject("Your Ticket for {$eventName} is Approved!")
                    ->html("
                        <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;\">
                            <h2 style=\"color: #1e3a8a; margin-bottom: 20px;\">Hello {$userName},</h2>
                            <p style=\"font-size: 16px; color: #334155; line-height: 1.6;\">
                                We are excited to inform you that your seat reservation request for the event <strong>{$eventName}</strong> has been approved!
                            </p>
                            <div style=\"margin: 25px 0; padding: 15px; background-color: #f1f5f9; border-radius: 8px; text-align: center;\">
                                <span style=\"font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block;\">Your Ticket Number</span>
                                <strong style=\"font-size: 22px; color: #0f172a; font-family: monospace;\">{$ticketNo}</strong>
                            </div>
                            <p style=\"font-size: 15px; color: #334155; margin-bottom: 10px;\">
                                Please present the QR code below at the entry gate to check in:
                            </p>
                            <div style=\"text-align: center; margin: 25px 0;\">
                                <img src=\"{$qrCodeCid}\" alt=\"Ticket QR Code\" style=\"border: 2px solid #cbd5e1; padding: 10px; border-radius: 12px; background-color: #fff; width: 220px; height: 220px;\" />
                            </div>
                            <hr style=\"border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;\" />
                            <p style=\"font-size: 15px; color: #334155;\">See you at the event!</p>
                            <p style=\"font-size: 15px; font-weight: bold; color: #0f172a; margin-top: 5px;\">
                                Best regards,<br/>
                                Sabha Team
                            </p>
                        </div>
                    ");
            });

            Log::info("SABHA Approved Ticket Email successfully dispatched to {$userEmail}. Ticket No: {$ticketNo}.");
        } catch (\Exception $e) {
            Log::error("Failed to send approval email to {$registration->user->email}: " . $e->getMessage());
        }

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
            $file->storeAs('gallery', $fileName, 'public');
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

    public function toggleAttendance($id)
    {
        $registration = EventRegistration::findOrFail($id);
        $registration->update([
            'is_attended' => !$registration->is_attended
        ]);
        return response()->json([
            'message' => 'Attendance status updated successfully',
            'registration' => $registration
        ]);
    }

    public function generateQrCode(Request $request)
    {
        $data = $request->query('data');
        if (empty($data)) {
            return response('Missing data parameter', 400);
        }

        $qrCode = new \App\Utils\QRCode($data, [
            's' => 'qrm', // Medium ECC
            'sf' => 8,    // Scale factor
            'p' => 2,     // Padding
        ]);

        $image = $qrCode->render_image();

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    public function checkInTicket(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string',
        ]);

        $registration = EventRegistration::where('ticket_number', $validated['ticket_number'])->first();

        if (!$registration) {
            return response()->json([
                'message' => 'Ticket not found.'
            ], 404);
        }

        if ($registration->status !== 'approved' && $registration->status !== 'confirmed') {
            return response()->json([
                'message' => 'Ticket is not approved yet. Current status: ' . $registration->status
            ], 400);
        }

        if ($registration->is_attended) {
            return response()->json([
                'message' => 'Ticket is already marked as attended.',
                'registration' => $registration->load('user', 'event')
            ], 200);
        }

        $registration->update([
            'is_attended' => true
        ]);

        return response()->json([
            'message' => 'Attendance marked successfully for ' . ($registration->user->name ?? 'attendee') . '!',
            'registration' => $registration->load('user', 'event')
        ]);
    }

    public function submitContactInquiry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'category' => 'nullable|string|max:255',
        ]);

        // Get admin contact email from settings
        $contactEmailSetting = Setting::where('key', 'contact_email')->first();
        $adminEmail = $contactEmailSetting ? $contactEmailSetting->value : config('mail.from.address', 'hello@sabha.global');
        
        $name = $validated['name'];
        $email = $validated['email'];
        $subject = $validated['subject'] ?? 'New Contact Inquiry';
        $inquiryMessage = $validated['message'];
        $category = $validated['category'] ?? 'General';

        // Send email to admin
        try {
            Mail::send([], [], function ($message) use ($adminEmail, $name, $email, $subject, $inquiryMessage, $category) {
                $message->to($adminEmail)
                    ->subject("New Inquiry: {$subject}")
                    ->html("
                        <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;\">
                            <h2 style=\"color: #1e3a8a; margin-bottom: 20px;\">New Contact Inquiry Received</h2>
                            <p style=\"font-size: 15px; color: #334155; line-height: 1.6;\">
                                <strong>Category:</strong> {$category}<br>
                                <strong>Name:</strong> {$name}<br>
                                <strong>Email:</strong> {$email}<br>
                                <strong>Subject:</strong> {$subject}<br>
                            </p>
                            <div style=\"margin: 20px 0; padding: 15px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;\">
                                <strong style=\"font-size: 14px; color: #475569; display: block; margin-bottom: 5px;\">Message:</strong>
                                <p style=\"font-size: 14px; color: #0f172a; margin: 0; white-space: pre-wrap;\">{$inquiryMessage}</p>
                            </div>
                            <hr style=\"border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;\" />
                            <p style=\"font-size: 12px; color: #64748b;\">This inquiry was submitted from the Contact form on the Sabha website.</p>
                        </div>
                    ");
            });

            // Optionally, send a confirmation email back to the user
            Mail::send([], [], function ($message) use ($email, $name, $subject) {
                $message->to($email)
                    ->subject("We received your inquiry: {$subject}")
                    ->html("
                        <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;\">
                            <h2 style=\"color: #1e3a8a; margin-bottom: 20px;\">Hello {$name},</h2>
                            <p style=\"font-size: 15px; color: #334155; line-height: 1.6;\">
                                Thank you for reaching out to us! We have received your inquiry regarding \"{$subject}\" and our team will get back to you as soon as possible.
                            </p>
                            <p style=\"font-size: 15px; color: #334155;\">
                                We usually respond within 1 business day.
                            </p>
                            <hr style=\"border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;\" />
                            <p style=\"font-size: 13px; color: #64748b;\">
                                Best regards,<br>
                                Sabha Team
                            </p>
                        </div>
                    ");
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Your inquiry has been sent successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send contact inquiry email: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send inquiry. Please try again later.'
            ], 500);
        }
    }

    public function submitBusinessInquiry(Request $request, $id)
    {
        $business = Business::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $name = $validated['name'];
        $email = $validated['email'];
        $subject = $validated['subject'];
        $inquiryMessage = $validated['message'];

        // Recipient is the business email, or the user's email who listed it, or admin fallback
        $recipientEmail = $business->email ?: ($business->user ? $business->user->email : null);
        if (!$recipientEmail) {
            $contactEmailSetting = Setting::where('key', 'contact_email')->first();
            $recipientEmail = $contactEmailSetting ? $contactEmailSetting->value : config('mail.from.address', 'hello@sabha.global');
        }

        try {
            // Send email to business owner
            Mail::send([], [], function ($message) use ($recipientEmail, $name, $email, $subject, $inquiryMessage, $business) {
                $message->to($recipientEmail)
                    ->subject("New Inquiry for {$business->name}: {$subject}")
                    ->html("
                        <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;\">
                            <h2 style=\"color: #1e3a8a; margin-bottom: 20px;\">New Business Inquiry Received</h2>
                            <p style=\"font-size: 15px; color: #334155; line-height: 1.6;\">
                                <strong>Business:</strong> {$business->name}<br>
                                <strong>Sender Name:</strong> {$name}<br>
                                <strong>Sender Email:</strong> {$email}<br>
                                <strong>Subject:</strong> {$subject}<br>
                            </p>
                            <div style=\"margin: 20px 0; padding: 15px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;\">
                                <strong style=\"font-size: 14px; color: #475569; display: block; margin-bottom: 5px;\">Message:</strong>
                                <p style=\"font-size: 14px; color: #0f172a; margin: 0; white-space: pre-wrap;\">{$inquiryMessage}</p>
                            </div>
                            <hr style=\"border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;\" />
                            <p style=\"font-size: 12px; color: #64748b;\">This inquiry was submitted from the Sabha Business Directory listing page for {$business->name}.</p>
                        </div>
                    ");
            });

            // Send confirmation email back to the user
            Mail::send([], [], function ($message) use ($email, $name, $subject, $business) {
                $message->to($email)
                    ->subject("Inquiry submitted to {$business->name}: {$subject}")
                    ->html("
                        <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff;\">
                            <h2 style=\"color: #1e3a8a; margin-bottom: 20px;\">Hello {$name},</h2>
                            <p style=\"font-size: 15px; color: #334155; line-height: 1.6;\">
                                Your inquiry has been successfully sent to <strong>{$business->name}</strong> regarding \"{$subject}\".
                            </p>
                            <p style=\"font-size: 15px; color: #334155;\">
                                The business owner will review your message and get back to you directly.
                            </p>
                            <hr style=\"border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;\" />
                            <p style=\"font-size: 13px; color: #64748b;\">
                                Best regards,<br>
                                Sabha Team
                            </p>
                        </div>
                    ");
            });

            return response()->json([
                'success' => true,
                'message' => 'Your inquiry has been sent successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send business inquiry email: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send inquiry. Please try again later.'
            ], 500);
        }
    }

    public function getHeroImages()
    {
        return response()->json(HeroImage::latest()->get());
    }

    public function storeHeroImage(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'title' => 'nullable|string',
            'caption' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('hero', $fileName, 'public');
            $imagePath = '/storage/hero/' . $fileName;
        } else {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        $heroImage = HeroImage::create([
            'image_path' => $imagePath,
            'title' => $validated['title'] ?? null,
            'caption' => $validated['caption'] ?? null,
        ]);

        return response()->json($heroImage, 201);
    }

    public function deleteHeroImage($id)
    {
        $heroImage = HeroImage::findOrFail($id);
        
        $path = public_path($heroImage->image_path);
        if (file_exists($path)) {
            @unlink($path);
        }

        $heroImage->delete();

        return response()->json(['message' => 'Hero image deleted successfully']);
    }

    // ─── Business Categories ──────────────────────────────────────────

    public function getCategories()
    {
        $categories = BusinessCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name');
        return response()->json($categories);
    }

    public function getAllCategories()
    {
        $categories = BusinessCategory::orderBy('sort_order')->get();
        $categoriesWithCounts = $categories->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'sort_order' => $cat->sort_order,
                'is_active' => $cat->is_active,
                'businesses_count' => \App\Models\Business::where('category', $cat->name)->count(),
            ];
        });
        return response()->json($categoriesWithCounts);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:business_categories,name',
        ]);

        $maxOrder = BusinessCategory::max('sort_order') ?? -1;

        $category = BusinessCategory::create([
            'name'       => $validated['name'],
            'sort_order' => $maxOrder + 1,
            'is_active'  => true,
        ]);

        return response()->json($category, 201);
    }

    public function deleteCategory($id)
    {
        $category = BusinessCategory::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
