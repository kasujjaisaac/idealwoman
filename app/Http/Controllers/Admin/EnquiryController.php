<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->validate(['status' => ['nullable', Rule::in(['new', 'in-progress', 'closed'])]])['status'] ?? null;

        return view('admin.enquiries', ['enquiries' => Enquiry::query()->when($status, fn ($query) => $query->where('status', $status))->latest('id')->paginate(15)->withQueryString()]);
    }

    public function update(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $enquiry->update($request->validate(['status' => ['required', Rule::in(['new', 'in-progress', 'closed'])]]));

        return back()->with('success', 'Enquiry status updated.');
    }
}
