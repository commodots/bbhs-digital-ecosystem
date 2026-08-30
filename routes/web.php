<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Authentication Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\MemberRegistrationController;
use App\Http\Controllers\Auth\MemberLoginController;

/*
|--------------------------------------------------------------------------
| Member Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\AlumniDirectoryController;
use App\Http\Controllers\MemberDashboardController;

/*
|--------------------------------------------------------------------------
| Public Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\NewsController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMemberController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Auth\AdminLoginController;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', fn () => Inertia::render('About'))->name('about');

Route::get('/alumni', fn () => Inertia::render('Alumni'))->name('alumni');

Route::get('/events', [EventController::class, 'index'])->name('events');

Route::get('/news', [NewsController::class, 'index'])->name('news');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects');

Route::get('/resources', fn () => Inertia::render('Resources'))->name('resources');

Route::get('/membership', fn () => Inertia::render('Membership'))->name('membership');

Route::get('/gallery', fn () => Inertia::render('Gallery'))->name('gallery');

Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', fn () => Inertia::render('Login'))->name('login');

Route::post('/login', [MemberLoginController::class, 'store'])
    ->name('login.store');

Route::post('/logout', [\App\Http\Controllers\Auth\MemberLoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', fn () => Inertia::render('Register'))->name('register');

Route::post('/register', [MemberRegistrationController::class, 'store'])
    ->name('member.register');


/*
|--------------------------------------------------------------------------
| Alumni Directory
|--------------------------------------------------------------------------
*/

Route::get('/alumni-directory', [AlumniDirectoryController::class, 'index'])
    ->name('alumni.directory');


/*
|--------------------------------------------------------------------------
| Public Detail Pages
|--------------------------------------------------------------------------
|
| Keep the existing demo/detail pages working while the public
| dynamic News/Event detail integration is finalized.
|
*/

Route::get('/events/{event:slug}', [EventController::class, 'show'])
    ->name('events.detail');

Route::get('/news/{news:slug}', [NewsController::class, 'show'])
    ->name('news.detail');

Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])
    ->name('projects.detail');


/*
|--------------------------------------------------------------------------
| Contact Form
|--------------------------------------------------------------------------
*/

Route::post('/contact', [ContactMessageController::class, 'store'])
    ->name('contact.store');


/*
|--------------------------------------------------------------------------
| Protected Member Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [MemberDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [MemberProfileController::class, 'show'])
        ->name('profile');

    Route::put('/profile', [MemberProfileController::class, 'update'])
        ->name('profile.update');
});


/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminLoginController::class, 'create'])
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login', [AdminLoginController::class, 'store'])
    ->middleware('guest')
    ->name('admin.login.store');

Route::redirect('/admin', '/admin/dashboard')
    ->middleware(['auth', 'admin']);

Route::redirect('/admin/index', '/admin/dashboard')
    ->middleware(['auth', 'admin']);
	
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Admin Home
        |--------------------------------------------------------------------------
        */

        Route::get('/', function () {
            return Redirect::route('admin.dashboard');
        })->name('index');

        Route::get('/index', function () {
            return Redirect::route('admin.dashboard');
        })->name('index.page');


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Members
        |--------------------------------------------------------------------------
        */

        Route::get('/members', [AdminMemberController::class, 'index'])
            ->name('members.index');

        Route::get('/members/{user}', [AdminMemberController::class, 'show'])
            ->name('members.show');

        Route::post('/members/{user}/approve', [AdminMemberController::class, 'approve'])
            ->name('members.approve');

        Route::post('/members/{user}/suspend', [AdminMemberController::class, 'suspend'])
            ->name('members.suspend');

        Route::post('/members/{user}/reject', [AdminMemberController::class, 'reject'])
            ->name('members.reject');


        /*
        |--------------------------------------------------------------------------
        | News
        |--------------------------------------------------------------------------
        */

        Route::resource('news', AdminNewsController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Events
        |--------------------------------------------------------------------------
        */

        Route::resource('events', AdminEventController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Projects
        |--------------------------------------------------------------------------
        */

        Route::resource('projects', AdminProjectController::class)
            ->except(['show']);
    });
