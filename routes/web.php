<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Site;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| الواجهة العامة
|--------------------------------------------------------------------------
*/
Route::get('/', [Site\HomeController::class, 'index'])->name('home');
Route::get('/article/{article}/{slug?}', [Site\ArticleController::class, 'show'])->whereNumber('article')->name('article.show');
Route::post('/article/{article}/comments', [Site\ArticleController::class, 'comment'])->whereNumber('article')->middleware('throttle:6,1')->name('article.comment');
Route::get('/section/{slug}', [Site\CategoryController::class, 'show'])->name('category.show');
Route::get('/tag/{slug}', [Site\TagController::class, 'show'])->name('tag.show');
Route::get('/author/{user}', [Site\AuthorController::class, 'show'])->whereNumber('user')->name('author.show');
Route::get('/latest', [Site\HomeController::class, 'latest'])->name('latest');
Route::get('/opinions', [Site\HomeController::class, 'opinions'])->name('opinions');
Route::get('/videos', [Site\HomeController::class, 'videos'])->name('videos');
Route::get('/search', [Site\SearchController::class, 'index'])->name('search');
Route::get('/page/{slug}', [Site\PageController::class, 'show'])->name('page.show');
Route::get('/contact', [Site\ContactController::class, 'create'])->name('contact');
Route::post('/contact', [Site\ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::post('/subscribe', [Site\SubscribeController::class, 'store'])->middleware('throttle:5,1')->name('subscribe');
Route::get('/ad/{ad}/click', [Site\AdController::class, 'click'])->name('ad.click');
Route::get('/rss.xml', [Site\FeedController::class, 'rss'])->name('rss');
Route::get('/sitemap.xml', [Site\FeedController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| لوحة التحكم
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile');
        Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

        // المواد الصحفية
        Route::middleware('can:articles.create')->group(function () {
            Route::get('articles/create', [Admin\ArticleController::class, 'create'])->name('articles.create');
            Route::post('articles', [Admin\ArticleController::class, 'store'])->name('articles.store');
        });
        Route::middleware('can:articles.create')->group(function () {
            Route::get('articles', [Admin\ArticleController::class, 'index'])->name('articles.index');
            Route::get('articles/{article}/edit', [Admin\ArticleController::class, 'edit'])->name('articles.edit');
            Route::put('articles/{article}', [Admin\ArticleController::class, 'update'])->name('articles.update');
            Route::delete('articles/{article}', [Admin\ArticleController::class, 'destroy'])->name('articles.destroy');
            Route::post('articles/{article}/submit', [Admin\ArticleController::class, 'submit'])->name('articles.submit');
            Route::post('articles/{article}/workflow', [Admin\ArticleController::class, 'workflow'])->name('articles.workflow');
        });
        Route::post('upload-image', [Admin\ArticleController::class, 'uploadImage'])->name('upload.image');

        // الأقسام والوسوم
        Route::middleware('can:taxonomy.manage')->group(function () {
            Route::resource('categories', Admin\CategoryController::class)->except('show');
            Route::resource('tags', Admin\TagController::class)->except('show');
        });

        Route::middleware('can:pages.manage')->resource('pages', Admin\PageController::class)->except('show');

        // التعليقات
        Route::middleware('can:comments.moderate')->group(function () {
            Route::get('comments', [Admin\CommentController::class, 'index'])->name('comments.index');
            Route::post('comments/{comment}/{status}', [Admin\CommentController::class, 'setStatus'])->whereIn('status', ['approved', 'pending', 'spam'])->name('comments.status');
            Route::delete('comments/{comment}', [Admin\CommentController::class, 'destroy'])->name('comments.destroy');
        });

        Route::middleware('can:messages.view')->group(function () {
            Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index');
            Route::get('messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
            Route::delete('messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');
        });

        Route::middleware('can:subscribers.manage')->group(function () {
            Route::get('subscribers', [Admin\SubscriberController::class, 'index'])->name('subscribers.index');
            Route::get('subscribers/export', [Admin\SubscriberController::class, 'export'])->name('subscribers.export');
            Route::delete('subscribers/{subscriber}', [Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');
        });

        Route::middleware('can:ads.manage')->resource('ads', Admin\AdController::class)->except('show');

        // النظام
        Route::middleware('can:users.manage')->resource('users', Admin\UserController::class)->except('show');
        Route::middleware('can:roles.manage')->resource('roles', Admin\RoleController::class)->except('show');
        Route::middleware('can:settings.manage')->group(function () {
            Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings');
            Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        });
        Route::get('logs', [Admin\ActivityLogController::class, 'index'])->middleware('can:logs.view')->name('logs');
    });
});
