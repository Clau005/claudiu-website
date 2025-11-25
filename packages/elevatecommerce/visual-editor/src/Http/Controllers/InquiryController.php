<?php

namespace ElevateCommerce\VisualEditor\Http\Controllers;

use ElevateCommerce\VisualEditor\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class InquiryController extends Controller
{
    /**
     * Store a new inquiry from contact form.
     */
    public function store(Request $request)
    {
        // Rate limiting: 3 submissions per 10 minutes per IP
        $key = 'inquiry:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'email' => "Too many submissions. Please try again in {$seconds} seconds.",
            ]);
        }

        // Validate required fields (all nullable to allow flexibility)
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:5000',
            'type' => 'nullable|string|in:general,support,sales,partnership',
            'source' => 'nullable|string|max:100',
        ]);

        // Require at least email or phone
        if (empty($validated['email']) && empty($validated['phone'])) {
            throw ValidationException::withMessages([
                'email' => 'Please provide either an email address or phone number.',
                'phone' => 'Please provide either an email address or phone number.',
            ]);
        }

        // Collect any additional custom fields
        $customFields = [];
        foreach ($request->all() as $key => $value) {
            if (!in_array($key, ['name', 'email', 'phone', 'company', 'subject', 'message', 'type', 'source', '_token'])) {
                $customFields[$key] = $value;
            }
        }

        // Create inquiry
        $inquiry = Inquiry::create([
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'] ?? null,
            'type' => $validated['type'] ?? 'general',
            'source' => $validated['source'] ?? 'contact-form',
            'custom_fields' => !empty($customFields) ? $customFields : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer'),
            'status' => 'new',
            'priority' => 'normal',
        ]);

        // Increment rate limiter
        RateLimiter::hit($key, 600); // 10 minutes

        // TODO: Send notification email to admin
        // TODO: Send confirmation email to customer

        return redirect()
            ->back()
            ->with('success', 'Thank you for your inquiry! We will get back to you soon.');
    }
}
