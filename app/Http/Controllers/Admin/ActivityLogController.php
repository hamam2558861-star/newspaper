<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->query('action'), fn ($q, $a) => $q->where('action', $a))
            ->when($request->query('user'), fn ($q, $u) => $q->where('user_id', $u))
            ->latest('created_at')->paginate(30)->withQueryString();

        return view('admin.logs', [
            'logs' => $logs,
            'actions' => ActivityLog::select('action')->distinct()->pluck('action'),
        ]);
    }
}
