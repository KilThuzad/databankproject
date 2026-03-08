<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\databank\LoginController;
use App\Http\Controllers\databank\ResearchProjectController;
use App\Http\Controllers\databank\DashboardController;
use App\Http\Controllers\databank\ProfileController;
use App\Http\Controllers\databank\EventController;
use App\Http\Controllers\databank\NotificationController;
use App\Http\Controllers\databank\AboutController;
use App\Http\Controllers\databank\AccountController;
use App\Http\Controllers\databank\UniversityController;
use App\Http\Controllers\databank\AgencyController;
use App\Http\Controllers\databank\CategoriesController;
use App\Http\Controllers\databank\SearchController;
use App\Http\Controllers\databank\ActivityLogsController;

use App\Http\Controllers\researcher\UserDashboardController;
use App\Http\Controllers\researcher\UserController;
use App\Http\Controllers\researcher\UserResearchProjectController;
use App\Http\Controllers\researcher\UserSearchController;
use App\Http\Controllers\researcher\UserEventsController;
use App\http\Controllers\researcher\ResearcherNotificationController;

use App\Http\Controllers\reviewer\ReviewerDashboardController;
use App\Http\Controllers\reviewer\ReviewerResearchesController;
use App\Http\Controllers\reviewer\ReviewerEventsController;
use App\Http\Controllers\reviewer\ReviewerBlogController;
use App\Http\Controllers\reviewer\ReviewerReviewController;
use App\Http\Controllers\reviewer\ReviewerProfileController;
use App\Http\Controllers\reviewer\ReviewerNotificationController;

use App\Http\Controllers\staff\StaffResearchProjectController;
use App\Http\Controllers\staff\StaffEventsController;
use App\Http\Controllers\staff\StaffDashboardController;
use App\Http\Controllers\staff\StaffSearchController;
use App\Http\Controllers\staff\StaffProfileController;
use App\Http\Controllers\staff\ReviewAssignmentController;
use App\Http\Controllers\staff\StaffNotificationController;





/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);

Route::get('/', fn () => view('welcome'));
Route::get('/sample', fn () => view('project.researcher.dashboard.sample'));


Route::get('/email/verify', function () {
    return view('auth.verify-email'); 
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard'); 
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');


Route::get('/verify-notice', function () {
    return view('auth.verify-notice-pending');
})->name('verification.notice.pending');

Route::get('/verify-email/{token}/{email}', [App\Http\Controllers\databank\LoginController::class, 'verifyPending'])
    ->name('verification.verify.pending');

Route::post('/verify-resend', [App\Http\Controllers\databank\LoginController::class, 'resendPendingVerification'])
    ->name('verification.resend.pending');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /* Profile */
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.updatePicture');
    });

    /* User Profile */
    Route::prefix('userprofile')->group(function () {
        Route::get('/', [UserController::class, 'show'])->name('userprofile.show');
        Route::get('/edit', [UserController::class, 'edit'])->name('userprofile.edit');
        Route::put('/update', [UserController::class, 'update'])->name('userprofile.update');
        Route::post('/userprofile/picture', [UserController::class, 'updatePicture'])->name('userprofile.updatePicture');
    });

    /* Notifications */
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    });

    /* About */
    Route::get('/about', [AboutController::class, 'about'])->name('about.des');
});



/*
|--------------------------------------------------------------------------
| Session-Checked Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['check.session'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | USER / RESEARCHER
    |--------------------------------------------------------------------------
    */
    Route::resource('member-agencies', AgencyController::class);

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/userdashboard', [UserDashboardController::class, 'showDashboard']);
    });

    Route::get('/user/research-projects', [UserResearchProjectController::class, 'index'])
    ->name('userresearchproject.index');
    Route::get('/userresearchproject/create', [UserResearchProjectController::class, 'create']);
    Route::resource('userresearchproject', UserResearchProjectController::class);
    Route::get('/user/research-projects/download/{id}', [UserResearchProjectController::class, 'download'])
     ->name('userresearchproject.download');

    Route::post('/user-research-project/{project}/add-member', [UserResearchProjectController::class, 'addMember'])
        ->name('userresearchproject.addMember');

    Route::post('/user-research-project/{project}/change-member', [UserResearchProjectController::class, 'changeMember'])
        ->name('userresearchproject.changeMember');

    Route::delete('/user-research-project/{project}/member/{member}/delete', [UserResearchProjectController::class, 'deleteMember'])
        ->name('userresearchproject.deleteMember');

    Route::post('/user-research-project/{project}/comment', [UserResearchProjectController::class, 'storecomment'])->name('userresearchproject.comment');
    Route::delete('/projects/{project}/comments/{comment}', [UserResearchProjectController::class, 'destroyComment'])
    ->name('user.researchproject.destroy');
    Route::post('/user/research-projects/{project}/comments/{comment}', [UserResearchProjectController::class, 'updateComment'])->name('user.researchproject.updateComment');
    Route::post('/user/research-projects/{project}/changeRole', [UserResearchProjectController::class, 'changeRole'])->name('userresearchproject.changeRole');
    Route::post('/user/research-projects/{project}/assignNewLeader', [UserResearchProjectController::class, 'assignNewLeader'])->name('userresearchproject.assignNewLeader');
    
    Route::middleware(['auth'])->group(function () {
            Route::get('/notifications', [ResearcherNotificationController::class, 'index'])
                ->name('researcher.notifications');

            Route::post('/notifications/read/{id}', [ResearcherNotificationController::class, 'markAsRead'])
                ->name('researcher.notifications.read');

            Route::post('/notifications/read-all', [ResearcherNotificationController::class, 'markAllAsRead'])
                ->name('researcher.notifications.readAll');

            Route::delete('/notifications/clear-all', [ResearcherNotificationController::class, 'clearAll'])
            ->name('researcher.notifications.clearAll');    
        });



        Route::middleware(['auth'])->group(function () {
            Route::get('/staff/notifications', [StaffNotificationController::class, 'index'])
                ->name('staff.notifications');

            Route::post('/staff/notifications/read/{id}', [StaffNotificationController::class, 'markAsRead'])
                ->name('staff.notifications.read');

            Route::post('/staff/notifications/read-all', [StaffNotificationController::class, 'markAllAsRead'])
                ->name('staff.notifications.readAll');

            Route::delete('/staff/notifications/clear-all', [StaffNotificationController::class, 'clearAll'])
            ->name('staff.notifications.clearAll');    
        });


        
        Route::middleware(['auth'])->group(function () {
            Route::get('/Reviewer/notifications', [ReviewerNotificationController::class, 'index'])
                ->name('Reviewer.notifications');

            Route::post('/Reviewer/notifications/read/{id}', [ReviewerNotificationController::class, 'markAsRead'])
                ->name('Reviewer.notifications.read');

            Route::post('/Reviewer/notifications/read-all', [ReviewerNotificationController::class, 'markAllAsRead'])
                ->name('Reviewer.notifications.readAll');

            Route::delete('/Reviewer/notifications/clear-all', [ReviewerNotificationController::class, 'clearAll'])
            ->name('Reviewer.notifications.clearAll');    
        });

    Route::post('/user/research-projects/{id}/re-uploadDoc', [UserResearchProjectController::class, 'uploadDocument'])->name('userresearchproject.reuploadDocument');
    


    Route::get('/UserSearch', [UserSearchController::class, 'search'])->name('userglobal.search');

    Route::get('/User/Events', [UserEventsController::class, 'index'])->name('userevents.index');
    Route::get('/calendar/events', [UserEventsController::class, 'getEvents'])->name('calendar.events');
    Route::get('/User/Calendar', [UserEventsController::class, 'showCalendar'])->name('userevents.calendar');
    Route::get('/User/events/{event}', [UserEventsController::class, 'show'])->name('userevents.show');


    Route::get('/activities', [ActivityLogsController::class, 'index'])->name('activities.index');

    /*
    |--------------------------------------------------------------------------
    | STAFF
    |--------------------------------------------------------------------------
    */

    Route::get('/Staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

    Route::get('/staff/researchproject', [StaffResearchProjectController::class, 'index'])->name('staffresearchproject.index');
    Route::get('/staffresearchproject/create', [StaffResearchProjectController::class, 'create']);
    Route::get('/staffresearchproject/{id}', [StaffResearchProjectController::class, 'show'])->name('staffresearchproject.show');
    Route::get('/staffresearchproject/{id}/edit', [StaffResearchProjectController::class, 'edit'])->name('staffresearchproject.edit');

    Route::put('/staffresearchproject/{id}/update', [StaffResearchProjectController::class, 'update'])->name('staffresearchproject.update');
    Route::put('/staffresearchproject/{id}/update-status', [StaffResearchProjectController::class, 'updateStatus'])->name('staffresearchproject.updateStatus');
    Route::put('/staffresearchproject/update-status-file', [StaffResearchProjectController::class, 'updateStatusFile'])->name('staffresearchproject.updateStatusFile');

    Route::put('/staff/projects/bulk-deadline', [App\Http\Controllers\staff\StaffResearchProjectController::class, 'setDeadlineByProject'])
    ->name('projects.bulkDeadlineByProject');

    Route::get('/staff/projects/pending', [StaffResearchProjectController::class, 'pending'])->name('staffresearchproject.pending');
    Route::get('/staff/projects/approved', [StaffResearchProjectController::class, 'approved'])->name('staffresearchproject.approved');

    Route::get('/staffsearchproject', [StaffSearchController::class, 'search'])->name('staffglobal.search');



    /*
    |--------------------------------------------------------------------------
    | REVIEWER
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard/project/{id}', [DashboardController::class, 'getProjectDetails'])->name('dashboard.project.details');

    Route::get('/reviewerDashboard', [ReviewerDashboardController::class, 'showDashboard'])->name('reviewer.dashboard.projects');
 
    Route::get('/reviewerResearches', [ReviewerResearchesController::class, 'index'])->name('reviewer.Researches');
    Route::get('/reviewer/Researches', [ReviewerResearchesController::class, 'blog'])->name('reviewer.blog');
    Route::get('/reviewerResearches/{id}', [ReviewerResearchesController::class, 'show'])->name('reviewer.Researches.show');
    Route::put('/research-projects/{project}/status', [ReviewerResearchesController::class, 'updateStatus'])
    ->name('reviewer.projects.updateStatus');

    Route::post('/reviewer/projects/{project}/comment', [ReviewerResearchesController::class, 'storeComment'])->name('research_projects.comment');
    Route::delete('/research-projects/{project}/comments/{comment}', [ReviewerResearchesController::class, 'destroyComment'])->name('research_projects.deleteComment');

    Route::put('/research-projects/{project}/comments/{comment}', [ReviewerResearchesController::class, 'updateComment'])
     ->name('research_projects.updateComment');

    Route::resource('/reviewer-events', ReviewerEventsController::class);
    Route::get('/reviewer-events/{event}', [ReviewerEventController::class, 'show'])->name('reviewer-events.show');
    Route::get('/reviewer/events/calendar', [ReviewerEventsController::class, 'showCalendar'])->name('reviewerEvents.calendar');

   Route::post('/reviewerResearches/{id}/upload-file', [ReviewerResearchesController::class, 'uploadFile'])->name('reviews.uploadFile');

    Route::get('/reviewer/profile', [ReviewerProfileController::class, 'show'])->name('reviewer.profile');
    Route::get('/reviewer/profile/edit', [ReviewerProfileController::class, 'edit'])->name('reviewer.profile.edit');
    Route::put('/reviewer/profile', [ReviewerProfileController::class, 'update'])->name('reviewer.profile.update');
    Route::put('/reviewer/profile/picture', [ReviewerProfileController::class, 'updatePicture'])->name('reviewer.profile.picture.update');    

    Route::get('/blog', [ReviewerBlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{id}', [ReviewerBlogController::class, 'show'])->name('blog.show');

    /*
    |--------------------------------------------------------------------------
    | ADMIN / DATABANK
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'getAllProject'])->name('dashboard.projects');

    Route::resource('research_projects', ResearchProjectController::class);

    Route::get('/research_projects/{id}', [ResearchProjectController::class, 'show'])->name('research_projects.show');
    Route::get('/research_projects/view-file/{filename}', [ResearchProjectController::class, 'viewFile'])->name('research_projects.viewFile');
    Route::get('/research_projects/download/{id}', [ResearchProjectController::class, 'download'])->name('research_projects.download');

    Route::put('/projects/deadlines/bulk', [ResearchProjectController::class, 'bulkDeadline'])->name('projects.bulkDeadline');



    /*
    |--------------------------------------------------------------------------
    | CATEGORIES & UNIVERSITIES
    |--------------------------------------------------------------------------
    */

    Route::resource('categories', CategoriesController::class);

    Route::prefix('universities')->group(function () {
        Route::get('/', [UniversityController::class, 'index'])->name('universities.index');
        Route::get('/create', [UniversityController::class, 'create'])->name('universities.create');
        Route::post('/', [UniversityController::class, 'store'])->name('universities.store');
        Route::get('/{university}/edit', [UniversityController::class, 'edit'])->name('universities.edit');
        Route::put('/{university}', [UniversityController::class, 'update'])->name('universities.update');
        Route::delete('/{university}', [UniversityController::class, 'destroy'])->name('universities.destroy');
    });



    /*
    |--------------------------------------------------------------------------
    | SEARCH & ACCOUNTS
    |--------------------------------------------------------------------------
    */

    Route::get('/search', [SearchController::class, 'search'])->name('global.search');

    Route::get('/accounts', [AccountController::class, 'getAllUsers'])->name('all.users');
    Route::delete('/account/{id}', [AccountController::class, 'deleteUSer'])->name('delete.user');
    Route::get('/accounts/{id}/edit', [AccountController::class, 'editUser'])->name('users.edit');
    Route::put('/accounts/{id}', [AccountController::class, 'updateUser'])->name('users.update');



    /*
    |--------------------------------------------------------------------------
    | CALENDAR
    |--------------------------------------------------------------------------
    */

    Route::resource('events', EventController::class);
    Route::get('/calendar', [EventController::class, 'calendar']);
    Route::get('/calendar/events', [EventController::class, 'getEvents'])->name('calendar.events');

    Route::get('/Viewcalendar', [EventController::class, 'showCalendar'])->name('events.calendar');
    Route::post('/Viewcalendar', [EventController::class, 'ajaxStore'])->name('events.ajaxStore');
    Route::put('/Viewcalendar/{id}', [EventController::class, 'ajaxUpdate'])->name('events.ajaxUpdate');
    Route::delete('/Viewcalendar/{id}', [EventController::class, 'ajaxDestroy'])->name('events.ajaxDestroy');

    Route::resource('staffevents', StaffEventsController::class)->parameters([
        'staffevents' => 'event',
    ]);

    Route::get('/staff/calendar', [StaffEventsController::class, 'calendar']);
    
    Route::get('/staff/calendar/events', [StaffEventsController::class, 'getEvents']);
    Route::get('/staff/Viewcalendar', [StaffEventsController::class, 'showCalendar'])->name('staffevents.calendar');

    Route::get('/staff/profile', [StaffProfileController::class, 'show'])
    ->name('staffprofile.show')
    ->middleware('auth');

    Route::get('/staff/profile/edit', [StaffProfileController::class, 'edit'])
    ->name('staffprofile.edit')
    ->middleware('auth');

    Route::put('/staff/profile/update', [StaffProfileController::class, 'update'])
    ->name('staffprofile.update')
    ->middleware('auth');

    Route::post('/staff/research-projects/{id}/upload-document', [StaffResearchProjectController::class, 'uploadDocument'])
    ->name('staffresearchproject.uploadDocument');

    Route::put('/staff/research-projects/{id}/update-approval-status', [StaffResearchProjectController::class, 'updateApprovalStatus'])
        ->name('staffresearchproject.updateApprovalStatus');

    Route::put('/staff/profile/update-picture', [StaffProfileController::class, 'updatePicture'])
    ->name('staffprofile.updatePicture')
    ->middleware('auth');

    Route::get('/projects/{project}/assign-reviewer', [StaffResearchProjectController::class, 'assignReviewerForm'])->name('staffreviewproject.assignReviewerForm');
    Route::post('/projects/{project}/assign-reviewer', [StaffResearchProjectController::class, 'storeReviewerAssignment'])->name('staffreviewproject.storeReviewerAssignment');

    Route::prefix('staff')->middleware(['auth'])->group(function () {
        Route::get('/review-assignments', [ReviewAssignmentController::class, 'index'])
            ->name('review_assignments.index');

        Route::get('/review-assignments/{project}/AssignR', [ReviewAssignmentController::class, 'AssignR'])
            ->name('review_assignments.AssignR');

        Route::post('/review-assignments/{project}/Assign', [ReviewAssignmentController::class, 'AssignedReviewer'])
            ->name('review_assignments.AssignedReviewer');
    });

    Route::put('/review-assignments/{id}', 
            [ReviewAssignmentController::class, 'update']
        )->name('review_assignments.update');

    Route::delete('/review_assignments/{id}', [ReviewAssignmentController::class, 'destroy'])->name('review_assignments.destroy');


    Route::post('reviewer/researches/{project}/submit-review', [ReviewerResearchesController::class, 'submitReview'])
        ->name('reviewer.Researches.submitReview');

    Route::get('/reviews', [ReviewerReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [ReviewerReviewController::class, 'show'])->name('reviews.show');

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', function () {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

});
