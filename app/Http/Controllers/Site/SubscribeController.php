<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:120']], [], ['email' => 'البريد الإلكتروني']);

        Subscriber::updateOrCreate(['email' => strtolower($data['email'])], ['is_active' => true]);

        return back()->with('subscribed', 'تم اشتراكك في النشرة البريدية بنجاح. شكراً لك!');
    }
}
