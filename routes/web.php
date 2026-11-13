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
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

/*
|--------------------------------------------------------------------------
| Member Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\AlumniDirectoryController;
use App\Http\Controllers\AlumniLandingController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WelfareController;
use App\Http\Controllers\MembershipDuesController;
use App\Http\Controllers\FundraisingController;

/*
|--------------------------------------------------------------------------
| Public Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventCalendarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HistoricalArchiveController;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFinancialReportController;
use App\Http\Controllers\Admin\AdminMemberController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\OrganisationController;
use App\Http\Controllers\Admin\MembershipVerificationController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminHistoricalArchiveController;
use App\Http\Controllers\Admin\AdminWelfareController;
use App\Http\Controllers\Admin\AdminMembershipDuesController;
use App\Http\Controllers\Admin\AdminFundraisingController;
use App\Http\Controllers\Admin\AdminContactDirectoryController;
use App\Http\Controllers\Admin\AdminGalleryController;
use App\Http\Controllers\Admin\AdminNewsletterController;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->middleware('throttle:6,1')->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/about', fn () => Inertia::render('About'))->name('about');

// Community pages
Route::get('/mentorship', fn () => Inertia::render('Mentorship'))->name('mentorship');
Route::get('/careers', fn () => Inertia::render('Careers'))->name('careers');
Route::get('/chapters', [ChaptersController::class, 'index'])->name('chapters');

// Preserve older mixed-case links by redirecting to canonical lowercase URLs.
Route::redirect('/Mentorship', '/mentorship', 301);
Route::redirect('/Careers', '/careers', 301);
Route::redirect('/Chapters', '/chapters', 301);
Route::get('/alumni', [AlumniLandingController::class, 'index'])->name('alumni');
Route::get('/executives', [\App\Http\Controllers\NationalExecutivesController::class, 'index'])->name('executives');
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/events/calendar', [EventCalendarController::class, 'index'])->name('events.calendar');
Route::get('/events/calendar.ics', [EventCalendarController::class, 'ics'])->name('events.calendar.ics');
Route::get('/news', [NewsController::class, 'index'])->name('news');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
Route::get('/fundraising', [FundraisingController::class, 'index'])->name('fundraising.index');
Route::get('/fundraising/paystack/callback', [FundraisingController::class, 'callback'])->name('fundraising.callback');
Route::get('/fundraising/{campaign:slug}', [FundraisingController::class, 'show'])->name('fundraising.show');
Route::post('/fundraising/{campaign:slug}/donate', [FundraisingController::class, 'donate'])->name('fundraising.donate');
Route::post('/fundraising/{campaign:slug}/pledge', [FundraisingController::class, 'pledge'])->name('fundraising.pledge');
Route::get('/resources', fn () => Inertia::render('Resources'))->name('resources');
Route::get('/archives', [HistoricalArchiveController::class, 'index'])->name('archives.index');
Route::get('/archives/{archive:slug}', [HistoricalArchiveController::class, 'show'])->name('archives.show');
Route::get('/membership', fn () => Inertia::render('Membership'))->name('membership');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => Inertia::render('Login'))->name('login');

// Email-based password recovery and first-time password setup.
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});


Route::post('/login', [MemberLoginController::class, 'store'])
    ->name('login.store');

Route::post('/logout', [MemberLoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', [MemberRegistrationController::class, 'create'])
    ->name('register');

Route::post('/register', [MemberRegistrationController::class, 'store'])
    ->name('member.register');

/*
|--------------------------------------------------------------------------
| Alumni Directory
|--------------------------------------------------------------------------
*/
Route::get('/alumni-directory', [AlumniDirectoryController::class, 'index'])
    ->middleware('auth')
    ->name('alumni.directory');

Route::get('/alumni-directory/{alumniProfile}', [AlumniDirectoryController::class, 'show'])
    ->middleware('auth')
    ->whereNumber('alumniProfile')
    ->name('alumni.directory.show');

/*
|--------------------------------------------------------------------------
| Public Detail Pages
|--------------------------------------------------------------------------
*/
Route::get('/events/{event:slug}', [EventController::class, 'show'])
    ->name('events.detail');

Route::middleware('auth')->group(function () {
    Route::post('/events/{event:slug}/register', [EventController::class, 'register'])
        ->name('events.register');
    Route::delete('/events/{event:slug}/register', [EventController::class, 'cancelRegistration'])
        ->name('events.registration.cancel');
});

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
| Protected Member Area and Notification Centre
|--------------------------------------------------------------------------
| Notifications are available to every authenticated user, including
| members, set/branch executives and Super Admins.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])
        ->name('dashboard');

    // Member fundraising activity
    Route::get('/my-fundraising', [FundraisingController::class, 'myActivity'])->name('fundraising.my-activity');

    // Membership dues
    Route::get('/dues', [MembershipDuesController::class, 'index'])->name('dues.index');
    Route::put('/dues/plan', [MembershipDuesController::class, 'choosePlan'])->name('dues.plan');
    Route::post('/dues/paystack/initialize', [MembershipDuesController::class, 'initialize'])->name('dues.paystack.initialize');
    Route::get('/dues/paystack/callback', [MembershipDuesController::class, 'callback'])->name('dues.callback');

    // Confidential member welfare applications
    Route::get('/welfare', [WelfareController::class, 'index'])->name('welfare.index');
    Route::get('/welfare/create', [WelfareController::class, 'create'])->name('welfare.create');
    Route::post('/welfare', [WelfareController::class, 'store'])->name('welfare.store');
    Route::get('/welfare/{application}', [WelfareController::class, 'show'])->name('welfare.show');
    Route::get('/welfare/{application}/attachments/{attachment}', [WelfareController::class, 'downloadAttachment'])->name('welfare.attachments.download');

    Route::get('/profile', [MemberProfileController::class, 'show'])
        ->name('profile');

    Route::put('/profile', [MemberProfileController::class, 'update'])
        ->name('profile.update');

    // Notification centre
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/{notification}/open', [NotificationController::class, 'open'])
        ->name('notifications.open');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.read-all');

    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});

// Paystack signed webhook. Configure this URI in the Paystack dashboard.
Route::post('/payments/paystack/webhook', [MembershipDuesController::class, 'webhook'])->name('paystack.webhook');

/*
|--------------------------------------------------------------------------
| Admin Login
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

/*
|--------------------------------------------------------------------------
| Super Admin Area
|--------------------------------------------------------------------------
| These routes require the existing auth + admin middleware.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Consolidated financial reporting
        Route::get('/financial-reports', [AdminFinancialReportController::class, 'index'])->name('financial-reports.index');
        Route::get('/financial-reports/export', [AdminFinancialReportController::class, 'export'])->name('financial-reports.export');

        // Newsletter subscribers and email updates (Super Admin only)
        Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
        Route::post('/newsletter/send', [AdminNewsletterController::class, 'send'])->name('newsletter.send');
        Route::get('/newsletter/export', [AdminNewsletterController::class, 'export'])->name('newsletter.export');

        // Admin home
        Route::get('/', function () {
            return Redirect::route('admin.dashboard');
        })->name('index');

        Route::get('/index', function () {
            return Redirect::route('admin.dashboard');
        })->name('index.page');

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Organisation, graduation sets, branches and executive appointments
        Route::get('/organisation', [OrganisationController::class, 'index'])
            ->name('organisation.index');

        Route::post('/organisation/assignments', [OrganisationController::class, 'storeAssignment'])
            ->name('organisation.assignments.store');

        Route::post('/organisation/positions', [OrganisationController::class, 'storePosition'])
            ->name('organisation.positions.store');

        Route::delete('/organisation/assignments/{assignment}', [OrganisationController::class, 'deactivateAssignment'])
            ->name('organisation.assignments.destroy');

        Route::post('/organisation/branches', [OrganisationController::class, 'storeBranch'])
            ->name('organisation.branches.store');

        Route::post('/organisation/branches/{branch}/cities', [OrganisationController::class, 'addCity'])
            ->name('organisation.branches.cities.store');

        Route::delete('/organisation/branches/{branch}/cities/{city}', [OrganisationController::class, 'removeCity'])
            ->name('organisation.branches.cities.destroy');

        // Members
        Route::get('/members', [AdminMemberController::class, 'index'])
            ->name('members.index');

        Route::get('/members/{user}', [AdminMemberController::class, 'show'])
            ->name('members.show');

        Route::patch('/members/{user}/email', [AdminMemberController::class, 'updateEmail'])
            ->name('members.email.update');
        Route::post('/members/{user}/send-password-link', [AdminMemberController::class, 'sendPasswordSetupLink'])
            ->name('members.password-link');

        Route::post('/members/{user}/approve', [AdminMemberController::class, 'approve'])
            ->name('members.approve');

        Route::post('/members/{user}/suspend', [AdminMemberController::class, 'suspend'])
            ->name('members.suspend');

        Route::post('/members/{user}/reject', [AdminMemberController::class, 'reject'])
            ->name('members.reject');

        // Historical archive CMS
        Route::get('/archives/{archive}/preview', [AdminHistoricalArchiveController::class, 'preview'])->name('archives.preview');
        Route::patch('/archives/{archive}/publish', [AdminHistoricalArchiveController::class, 'togglePublished'])->name('archives.publish');
        Route::patch('/archives/{archive}/feature', [AdminHistoricalArchiveController::class, 'toggleFeatured'])->name('archives.feature');
        Route::resource('archives', AdminHistoricalArchiveController::class)->except(['show']);

        // News
        Route::resource('news', AdminNewsController::class)
            ->except(['show']);

        // Events
        Route::get('/events/{event}/registrations/export', [AdminEventController::class, 'exportRegistrations'])
            ->name('events.registrations.export');
        Route::get('/events/{event}/registrations', [AdminEventController::class, 'registrations'])
            ->name('events.registrations');
        Route::resource('events', AdminEventController::class)
            ->except(['show']);

        // Projects
        Route::resource('projects', AdminProjectController::class)
            ->except(['show']);

        // Gallery management
        Route::get('/gallery', [AdminGalleryController::class, 'index'])->name('gallery.index');
        Route::post('/gallery/albums', [AdminGalleryController::class, 'storeAlbum'])->name('gallery.albums.store');
        Route::post('/gallery/albums/{album}', [AdminGalleryController::class, 'updateAlbum'])->name('gallery.albums.update');
        Route::post('/gallery/albums/{album}/images', [AdminGalleryController::class, 'uploadImages'])->name('gallery.images.store');
        Route::delete('/gallery/images/{image}', [AdminGalleryController::class, 'deleteImage'])->name('gallery.images.destroy');
        Route::delete('/gallery/albums/{album}', [AdminGalleryController::class, 'destroyAlbum'])->name('gallery.albums.destroy');

        // Membership dues administration (Super Admin only)
        Route::get('/dues', [AdminMembershipDuesController::class, 'index'])->name('dues.index');
        Route::get('/dues/{user}', [AdminMembershipDuesController::class, 'show'])->name('dues.show');
        Route::put('/dues/{user}', [AdminMembershipDuesController::class, 'update'])->name('dues.update');
        Route::post('/dues/{user}/remind', [AdminMembershipDuesController::class, 'remind'])->name('dues.remind');
        Route::post('/dues/remind-bulk', [AdminMembershipDuesController::class, 'remindBulk'])->name('dues.remind-bulk');

        // Fundraising campaigns, donations and pledges
        Route::get('/fundraising', [AdminFundraisingController::class, 'index'])->name('fundraising.index');
        Route::get('/fundraising/create', [AdminFundraisingController::class, 'create'])->name('fundraising.create');
        Route::post('/fundraising', [AdminFundraisingController::class, 'store'])->name('fundraising.store');
        Route::get('/fundraising/donations', [AdminFundraisingController::class, 'donations'])->name('fundraising.donations');
        Route::get('/fundraising/pledges', [AdminFundraisingController::class, 'pledges'])->name('fundraising.pledges');
        Route::post('/fundraising/donations/{donation}/verify', [AdminFundraisingController::class, 'verifyDonation'])->name('fundraising.donations.verify');
        Route::get('/fundraising/{campaign}/edit', [AdminFundraisingController::class, 'edit'])->name('fundraising.edit');
        Route::put('/fundraising/{campaign}', [AdminFundraisingController::class, 'update'])->name('fundraising.update');
        Route::delete('/fundraising/{campaign}', [AdminFundraisingController::class, 'destroy'])->name('fundraising.destroy');

        // Welfare applications (confidential; global admin access)
        Route::get('/welfare', [AdminWelfareController::class, 'index'])->name('welfare.index');
        Route::get('/welfare/{application}', [AdminWelfareController::class, 'show'])->name('welfare.show');
        Route::patch('/welfare/{application}', [AdminWelfareController::class, 'update'])->name('welfare.update');
        Route::post('/welfare/{application}/disbursements', [AdminWelfareController::class, 'disburse'])->name('welfare.disbursements.store');
        Route::get('/welfare/{application}/attachments/{attachment}', [AdminWelfareController::class, 'downloadAttachment'])->name('welfare.attachments.download');

        // Super Admin notification announcement composer
        Route::get('/notifications/create', [AdminNotificationController::class, 'create'])
            ->name('notifications.create');

        Route::post('/notifications', [AdminNotificationController::class, 'store'])
            ->name('notifications.store');
    });

/*
|--------------------------------------------------------------------------
| Membership Verification and Executive Actions
|--------------------------------------------------------------------------
| This group is deliberately outside the global admin group so Set and
| Branch executives can access only the queues permitted by their scope.
| The controller further restricts the reminder action to Super Admin.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'executive'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/contacts', [AdminContactDirectoryController::class, 'index'])
            ->name('contacts.index');

        Route::get('/verifications', [MembershipVerificationController::class, 'index'])
            ->name('verifications.index');

        Route::post('/verifications/{verification}/approve', [MembershipVerificationController::class, 'approve'])
            ->name('verifications.approve');

        Route::post('/verifications/{verification}/reject', [MembershipVerificationController::class, 'reject'])
            ->name('verifications.reject');

        // Super Admin sends reminders to the responsible Set/Branch executives.
        Route::post('/verifications/remind', [MembershipVerificationController::class, 'remind'])
            ->name('verifications.remind');
    });
