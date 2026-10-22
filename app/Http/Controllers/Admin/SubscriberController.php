<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $subs = Subscriber::when($request->query('q'), fn ($q, $s) => $q->where('email', 'like', "%$s%"))
            ->latest()->paginate(30)->withQueryString();

        return view('admin.subscribers.index', compact('subs'));
    }

    public function export()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['البريد الإلكتروني', 'الحالة', 'تاريخ الاشتراك']);
            Subscriber::orderBy('id')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, [$r->email, $r->is_active ? 'فعال' : 'متوقف', $r->created_at->format('Y-m-d')]);
                }
            });
            fclose($out);
        }, 'subscribers-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'تم حذف المشترك.');
    }
}
