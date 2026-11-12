<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentRevision;
use App\Models\ContentSection;
use App\Models\Enquiry;
use App\Services\SiteContent;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(SiteContent $content): View
    {
        return view('admin.dashboard', ['definitions' => $content->definitions(), 'sections' => ContentSection::query()->get()->keyBy('slug'), 'revisions' => ContentRevision::query()->with('author')->latest('id')->limit(8)->get(), 'newEnquiries' => Enquiry::query()->where('status', 'new')->count()]);
    }
}
