<?php

use Illuminate\Support\Facades\Route;
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
| Admin Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMemberController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminEventController;


/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');


Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');


Route::get('/alumni', function () {
    return Inertia::render('Alumni');
})->name('alumni');


Route::get('/events', function () {
    return Inertia::render('Events');
})->name('events');


Route::get('/news', function () {
    return Inertia::render('News');
})->name('news');


Route::get('/projects', function () {
    return Inertia::render('Projects');
})->name('projects');


Route::get('/resources', function () {
    return Inertia::render('Resources');
})->name('resources');


Route::get('/membership', function () {
    return Inertia::render('Membership');
})->name('membership');


Route::get('/gallery', function () {
    return Inertia::render('Gallery');
})->name('gallery');


Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');


Route::post('/login', [MemberLoginController::class, 'store'])
    ->name('login.store');


/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [MemberLoginController::class, 'destroy'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Registration
|--------------------------------------------------------------------------
*/

Route::get('/register', function () {
    return Inertia::render('Register');
})->name('register');


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
| Public Event Detail
|--------------------------------------------------------------------------
*/

Route::get('/events/reunion', function () {
    return Inertia::render('EventDetail');
})->name('events.detail');


/*
|--------------------------------------------------------------------------
| Public News Detail
|--------------------------------------------------------------------------
*/

Route::get('/news/story', function () {
    return Inertia::render('NewsDetail');
})->name('news.detail');


/*
|--------------------------------------------------------------------------
| Public Project Detail
|--------------------------------------------------------------------------
*/

Route::get('/projects/supporting-the-future', function () {
    return Inertia::render('ProjectDetail');
})->name('projects.detail');


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

    /*
    |--------------------------------------------------------------------------
    | Member Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [MemberDashboardController::class, 'index'])
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Member Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [MemberProfileController::class, 'show'])
        ->name('profile');


    Route::put('/profile', [MemberProfileController::class, 'update'])
        ->name('profile.update');

});


/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
|
| All routes below require:
|
| 1. Authentication
| 2. Admin privileges
|
| URL prefix:
| /admin
|
| Route name prefix:
| admin.
|
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Admin Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Member Management
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
        | News Management
        |--------------------------------------------------------------------------
        |
        | Creates:
        |
        | GET       /admin/news
        | GET       /admin/news/create
        | POST      /admin/news
        | GET       /admin/news/{news}/edit
        | PUT       /admin/news/{news}
        | PATCH     /admin/news/{news}
        | DELETE    /admin/news/{news}
        |
        */

        Route::resource('news', AdminNewsController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Events Management
        |--------------------------------------------------------------------------
        |
        | Creates:
        |
        | GET       /admin/events
        | GET       /admin/events/create
        | POST      /admin/events
        | GET       /admin/events/{event}/edit
        | PUT       /admin/events/{event}
        | PATCH     /admin/events/{event}
        | DELETE    /admin/events/{event}
        |
        */

        Route::resource('events', AdminEventController::class)
            ->except(['show']);

    });