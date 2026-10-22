<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('pagination.site');

        // مدير النظام يتجاوز كل الفحوصات
        Gate::before(fn (User $user) => $user->isSuperAdmin() && $user->is_active ? true : null);

        // كل صلاحية معرّفة في config/newsroom.php تصبح Gate بنفس الاسم
        foreach (config('newsroom.permissions') as $group) {
            foreach (array_keys($group) as $permission) {
                Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
            }
        }

        // بيانات مشتركة للواجهة العامة (القائمة، العاجل، التذييل)
        View::composer('layouts.site', function ($view) {
            if (! Schema::hasTable('categories')) {
                return;
            }
            $view->with([
                'menuCategories' => Category::active()->whereNull('parent_id')->where('show_in_menu', true)
                    ->with(['children' => fn ($q) => $q->where('is_active', true)])->orderBy('sort_order')->get(),
                'breakingNews' => Article::published()->where('is_breaking', true)->latest('published_at')->limit(8)->get(['id', 'title', 'slug', 'published_at']),
                'footerPages' => Page::where('is_active', true)->where('show_in_footer', true)->get(['title', 'slug']),
            ]);
        });
    }
}
