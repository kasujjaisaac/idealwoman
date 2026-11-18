<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnquiryRequest;
use App\Mail\PartnershipEnquiry;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnquiryController extends Controller
{
    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        $enquiry = $request->safe()->only(['name', 'email', 'organisation', 'enquiry_type', 'message']);
        $failure = 'Your message has not been sent. Please email '.config('idealwoman.enquiries_to').' or call +256 700 947613.';
        if (! config('idealwoman.enquiries_enabled') || config('mail.default') !== 'smtp') {
            return back()->withInput($request->safe()->except('website'))->with('delivery_error', $failure);
        }

        try {
            $sent = Mail::to(config('idealwoman.enquiries_to'))->send(new PartnershipEnquiry($enquiry));
            if ($sent === null) {
                return back()->withInput($request->safe()->except('website'))->with('delivery_error', $failure);
            }
        } catch (Throwable $exception) {
            Log::warning('Enquiry mail transport failed.', ['exception_type' => $exception::class]);

            return back()->withInput($request->safe()->except('website'))->with('delivery_error', $failure);
        }

        Enquiry::create($enquiry);

        return to_route($request->validated('return_to', 'contact'))->with('success', 'Thank you. Your enquiry has been accepted by our mail service for delivery to the Ideal Woman team.');
    }
}
