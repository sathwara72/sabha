<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    private const FALLBACK_COORDINATORS = [
        ['city' => 'Mumbai Coordinator', 'contact' => 'Ravi Sharma', 'phone' => '+91 98200 12345', 'email' => 'mumbai@sabha.global', 'bg' => 'bg-blue-50/50', 'border' => 'border-blue-100'],
        ['city' => 'Pune Coordinator', 'contact' => 'Pooja Verma', 'phone' => '+91 96110 54321', 'email' => 'pune@sabha.global', 'bg' => 'bg-emerald-50/50', 'border' => 'border-emerald-100'],
        ['city' => 'Ahmedabad Coordinator', 'contact' => 'Dev Patel', 'phone' => '+91 94260 98765', 'email' => 'ahmedabad@sabha.global', 'bg' => 'bg-amber-50/50', 'border' => 'border-amber-100'],
    ];

    private const COORDINATOR_STYLES = [
        ['bg' => 'bg-blue-50/50', 'border' => 'border-blue-100'],
        ['bg' => 'bg-emerald-50/50', 'border' => 'border-emerald-100'],
        ['bg' => 'bg-amber-50/50', 'border' => 'border-amber-100'],
    ];

    public function show(): View
    {
        $settings = Setting::all()->pluck('value', 'key');

        $coordinators = self::FALLBACK_COORDINATORS;
        if ($settings->get('coordinators')) {
            $raw = $settings->get('coordinators');
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            if (! empty($decoded)) {
                $coordinators = collect($decoded)->values()->map(function ($c, $idx) {
                    return array_merge((array) $c, self::COORDINATOR_STYLES[$idx % 3]);
                })->all();
            }
        }

        return view('pages.contact', [
            'contactEmail' => $settings->get('contact_email') ?: 'hello@sabha.global',
            'responseTime' => $settings->get('response_time') ?: 'Within 1 Business Day',
            'coordinators' => $coordinators,
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'category' => 'nullable|string|max:255',
        ]);

        $adminEmail = Setting::where('key', 'contact_email')->value('value')
            ?? config('mail.from.address', 'hello@sabha.global');

        $name = $validated['name'];
        $email = $validated['email'];
        $subject = $validated['subject'] ?? 'New Contact Inquiry';
        $inquiryMessage = $validated['message'];
        $category = $validated['category'] ?? 'General';

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
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            report($e);

            return response()->json(['message' => 'Failed to send message. Please try again.'], 500);
        }

        return response()->json(['message' => 'Inquiry sent successfully!']);
    }
}
