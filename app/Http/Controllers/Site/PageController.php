<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        return view('site.page', ['page' => Page::where('slug', $slug)->where('is_active', true)->firstOrFail()]);
    }
}
