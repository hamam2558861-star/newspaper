<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        return view('site.contact');
    }

    public function store(Request $request)
    {
        if ($request->filled('website')) {
            return back();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:120'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ], [], ['name' => 'الاسم', 'email' => 'البريد الإلكتروني', 'subject' => 'الموضوع', 'message' => 'الرسالة']);

        ContactMessage::create($data);

        return back()->with('success', 'وصلتنا رسالتك، شكراً لتواصلك. سنرد عليك في أقرب وقت.');
    }
}
