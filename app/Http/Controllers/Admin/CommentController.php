<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $comments = Comment::with('article:id,title,slug')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.comments.index', [
            'comments' => $comments,
            'status' => $status,
            'counts' => Comment::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function setStatus(Comment $comment, string $status)
    {
        $comment->update(['status' => $status]);

        return back()->with('success', 'تم تحديث حالة التعليق.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'تم حذف التعليق.');
    }
}
