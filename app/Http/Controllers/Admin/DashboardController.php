<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $seeAll = $user->can('articles.edit_all') || $user->can('articles.publish');
        $base = fn () => $seeAll ? Article::query() : Article::where('user_id', $user->id);

        $stats = [
            'published' => $base()->published()->count(),
            'pending'   => $base()->where('status', 'pending')->count(),
            'drafts'    => $base()->where('status', 'draft')->count(),
            'views'     => (int) $base()->sum('views'),
        ];

        $extra = [];
        if ($user->can('comments.moderate')) {
            $extra['pending_comments'] = Comment::where('status', 'pending')->count();
        }
        if ($user->can('messages.view')) {
            $extra['unread_messages'] = ContactMessage::where('is_read', false)->count();
        }
        if ($user->can('subscribers.manage')) {
            $extra['subscribers'] = Subscriber::where('is_active', true)->count();
        }
        if ($user->can('users.manage')) {
            $extra['users'] = User::count();
        }

        // المشاهدات: أعلى المواد قراءةً هذا الأسبوع + عدد المواد المنشورة يومياً (14 يوم)
        $top = $base()->published()->where('published_at', '>=', now()->subDays(30))->orderByDesc('views')->limit(6)->get(['id', 'title', 'slug', 'views']);

        $daily = $base()->published()
            ->where('published_at', '>=', now()->subDays(13)->startOfDay())
            ->select(DB::raw('DATE(published_at) as d'), DB::raw('COUNT(*) as c'))
            ->groupBy('d')->pluck('c', 'd');
        $chart = collect(range(13, 0))->map(fn ($i) => [
            'label' => now()->subDays($i)->format('d/m'),
            'value' => (int) ($daily[now()->subDays($i)->format('Y-m-d')] ?? 0),
        ]);

        $queue = $user->can('articles.publish')
            ? Article::where('status', 'pending')->with(['author', 'category'])->latest('updated_at')->limit(6)->get()
            : collect();

        $mine = Article::where('user_id', $user->id)->with('category')->latest('updated_at')->limit(6)->get();

        $logs = $user->can('logs.view') ? ActivityLog::with('user')->latest('created_at')->limit(8)->get() : collect();

        return view('admin.dashboard', compact('stats', 'extra', 'top', 'chart', 'queue', 'mine', 'logs'));
    }
}
