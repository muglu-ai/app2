<?php

use Illuminate\Support\Facades\Route;
use Mews\Captcha\Facades\Captcha;

use App\Http\Controllers\{
    AdminController,
    AllocationController,
    ApplicationController,
    AttendeeController,
    AuthController,
    CoExhibitorController,
    DashboardController,
    ExhibitorController,
    ExhibitorInfoController,
    ExportController,
    ExtraRequirementController,
    ForgotPasswordController,
    InvoicesController,
    MailController,
    MisController,
    PayPalController,
    PaymentController,
    PaymentGatewayController,
    PaymentReceiptController,
    SalesController,
    SponsorController,
    SponsorshipController,
    TicketController
};
use App\Http\Middleware\{Auth, CheckUser};

// Routes without middleware
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/captcha', fn() => view('captcha.view'))->name('captcha.view');
Route::get('/reload-captcha', fn() => response()->json(['captcha' => captcha_img()]))->name('captcha.reload');
Route::get('/thank-you', fn() => view('attendee.thank-you'))->name('thank-you');
Route::get('/sponsorship-test', fn() => view('sponsor.page'));
Route::get('/co-exhibitor/dashboard', fn() => view('co_exhibitor.dashboard'))->name('dashboard.co-exhibitor');
Route::view('/about', 'pages.home')->name('about');
Route::view('/application-from', 'pages.form')->name('application-form');
Route::get('review_new', fn() => view('applications.preview_new'));
Route::get('/pgway', fn() => view('pgway.create-order'));
Route::match(['get', 'post'], '/ccavResponseHandler', fn() => request()->all());
Route::get('/send-invoice/{invoiceId}/{email}', function ($invoiceId, $email) {
    return app()->call([PaymentGatewayController::class, 'sendInvoice'], [
        'invoiceId' => $invoiceId,
        'toEmail' => $email
    ]);
});
Route::get('/terms-conditions', fn() => view('applications.tc'))->name('terms-conditions');
Route::get('/mail-test', [MailController::class, 'mailTest'])->name('mail.test');
Route::post('/send-email', [MailController::class, 'sendEmail'])->name('send.email');
Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
Route::post('/get-sqm-options', [ApplicationController::class, 'getSQMOptions']);
Route::post('/get-country-code', [ApplicationController::class, 'getCountryCode']);
Route::resource('tickets', TicketController::class);

// Visitor/Attendee routes (no middleware)
Route::get('/visitor/registration', [AttendeeController::class, 'showForm'])->name('visitor.register.form');
Route::post('/visitor/registration', [AttendeeController::class, 'visitor_reg'])->name('visitor_register');
Route::get('/visitor/thankyou/{id}', [AttendeeController::class, 'thankyou'])->name('visitor_thankyou');

// Password/Account routes (no middleware)
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('forgot.password');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('forgot.password.submit');
Route::get('reset-password/{token}/{email}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset.password.submit');
Route::get('verify-account/{token}', [AuthController::class, 'verifyAccount'])->name('auth.verify');

// Payment/PayPal routes (no middleware)
Route::get('/payment/{id}', [PayPalController::class, 'showPaymentForm'])->name('paypal.form');
Route::post('/paypal/create', [PayPalController::class, 'createOrder'])->name('paypal.create');
Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order/{orderId}', [PayPalController::class, 'captureOrder']);
Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
Route::post('/payment/ccavenue/{id}', [PaymentGatewayController::class, 'ccAvenuePayment']);
Route::post('/exhibitor-payment/ccavenue/{id}', [PaymentGatewayController::class, 'exhibitor_ccAvenuePayment'])->name('exhibitor.ccavenue.payment');
Route::post('/payment/ccavenue-success', [PaymentGatewayController::class, 'ccAvenueSuccess'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/paypal/webhook', [PayPalController::class, 'webhook'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('testtt', [PaymentGatewayController::class, 'ccAvenueTestSuccess']);

// Routes with CheckUser middleware
Route::middleware([CheckUser::class])->group(function () {
    Route::get('/exhibitor-info', [ExhibitorInfoController::class, 'showForm'])->name('exhibitor.info');
    Route::post('/exhibitor-info', [ExhibitorInfoController::class, 'storeExhibitor'])->name('exhibitor.info.submit');
    Route::get('/product-add', [ExhibitorInfoController::class, 'showProductForm'])->name('product.add');
    Route::post('/product-add', [ExhibitorInfoController::class, 'productStore'])->name('product.store');
    Route::post('extra_requirements', [ExtraRequirementController::class, 'store'])->name('extra_requirements.store');
    Route::get('exhibitor/orders', [ExtraRequirementController::class, 'userOrders'])->name('exhibitor.orders');
    Route::get('/co-exhibitor', [CoExhibitorController::class, 'user_list'])->name('co_exhibitor');
    Route::post('/co-exhibitor/store', [CoExhibitorController::class, 'store'])->name('co_exhibitor.store');
    Route::match(['post', 'get'], '/application/exhibitor', [ApplicationController::class, 'showForm'])->name('application.exhibitor');
    Route::post('/exhibitor/application', [ApplicationController::class, 'submitForm'])->name('application.exhibitor.submit');
    Route::get('apply', [ApplicationController::class, 'apply'])->name('application.show');
    Route::get('apply_new2', [ApplicationController::class, 'apply_spon'])->name('application.show2');
    Route::post('apply', [ApplicationController::class, 'apply_store'])->name('application.submit.store');
    Route::get('terms', [ApplicationController::class, 'terms'])->name('terms');
    Route::post('terms', [ApplicationController::class, 'terms_store'])->name('terms.store');
    Route::get('preview', [ApplicationController::class, 'preview'])->name('application.preview');
    Route::get('/exhibitor/application/review', [ApplicationController::class, 'review'])->name('application.review');
    Route::get('dashboard', [DashboardController::class, 'exhibitorDashboard'])->name('user.dashboard');
    Route::get('/event-list', [AuthController::class, 'showEvents'])->name('event.list');
    Route::match(['post', 'get'], '/proforma/{application_id}', [ApplicationController::class, 'invoice'])->name('invoice-details');
    Route::post('/invite', [ExhibitorController::class, 'invite'])->name('exhibition.invite');
    Route::post('/add', [ExhibitorController::class, 'add'])->name('exhibition.invite');
    Route::get('/exhibitor/list/{type}', [ExhibitorController::class, 'list'])->name('exhibition.list');
    Route::get('application-info', [ApplicationController::class, 'applicationInfo'])->name('application.info');
    Route::get('invoices', [ExhibitorController::class, 'invoices'])->name('exhibitor.invoices');
    Route::post('upload-receipt-user', [PaymentReceiptController::class, 'uploadReceipt_user'])->name('upload.receipt_user');
    Route::get('/{event}/onboarding', [ApplicationController::class, 'showForm'])->name('new_form');
    Route::get('/{event}/sponsorship', [SponsorController::class, 'new'])->name('sponsorship');
    Route::get('/{event}/sponsorship_test', [SponsorController::class, 'new_up'])->name('list_sponsorship_test');
    Route::get('/{event}/sponsorship_new', [SponsorshipController::class, 'listing'])->name('list_sponsorship_new');
    Route::post('/submit_sponsor', [SponsorshipController::class, 'store'])->name('sponsor.store');
    Route::get('/sponsor/preview', [SponsorshipController::class, 'confirmation'])->name('sponsor.review');
    Route::post('/sponsor/delete', [SponsorshipController::class, 'delete'])->name('sponsor.delete');
    Route::post('/sponsor/submit', [SponsorController::class, 'submit'])->name('sponsor.submit');
    Route::get('apply_new', [ApplicationController::class, 'apply_new'])->name('apply_new');
    Route::get('review_sponsor', [SponsorController::class, 'review'])->name('review.sponsor');
    Route::match(['post', 'get'], '/payment', [PaymentController::class, 'showOrder'])->name('payment');
    Route::post('/payment/partial', [PaymentController::class, 'partialPayment'])->name('payment.partial');
    Route::post('/payment/full', [PaymentController::class, 'fullPayment'])->name('payment.full');
    Route::match(['post', 'get'], '/payment/verify', [PaymentController::class, 'Successpayment'])->name('payment.verify');
});

// Routes with Auth middleware
Route::middleware([Auth::class])->group(function () {
    Route::get('admin_attendee_list', [AttendeeController::class, 'listAttendees'])->name('visitor.list');
    Route::get('export-attendees', [AttendeeController::class, 'export'])->name('export.list');
    Route::get('/application-list/', [AdminController::class, 'index'])->name('application.lists');
    Route::get('/copy-application/', [AdminController::class, 'copy'])->name('application.copy');
    Route::get('/application-list/{status}', [AdminController::class, 'index'])->name('application.list');
    Route::get('/application-detail', [DashboardController::class, 'applicantDetails'])->name('application.show.admin');
    Route::get('/price', [AdminController::class, 'price'])->name('price');
    Route::post('/approve/{id}', [AdminController::class, 'approve'])->name('approve');
    Route::get('/invoice-list', [DashboardController::class, 'invoiceDetails'])->name('invoice.list');
    Route::view('/users/list', 'admin.users')->name('users.list');
    Route::get('/get-users', [AdminController::class, 'getUsers']);
    Route::post('/application/submit', [AdminController::class, 'approve'])->name('approve.submit');
    Route::get('/application/submit/test', [AdminController::class, 'approve_test'])->name('approve.submit.test');
    Route::post('/sponsorship/submit', [SponsorController::class, 'approve'])->name('sponsorship.submit');
    Route::post('/application/reject', [AdminController::class, 'reject'])->name('reject.submit');
    Route::post('/sponsorship/reject', [AdminController::class, 'sponsorship_reject'])->name('sponsorship.reject');
    Route::post('/payment/success', [PaymentController::class, 'completeOrder'])->name('payment_success');
    Route::post('upload-receipt', [PaymentReceiptController::class, 'uploadReceipt'])->name('upload.receipt');
    Route::get('/sponsor/create_new', [SponsorController::class, 'create'])->name('sponsor.create_new');
    Route::get('/sponsor/add', [SponsorController::class, 'add'])->name('sponsor.add');
    Route::get('/sponsor/{id}/update', [SponsorController::class, 'sponsor_update'])->name('sponsor.update');
    Route::post('/sponsor/store', [SponsorController::class, 'create'])->name('sponsor_item.store');
    Route::post('/sponsor-items/store', [SponsorController::class, 'item_store'])->name('sponsor_items.store');
    Route::put('/sponsor-items/{id}/update', [SponsorController::class, 'item_update'])->name('sponsor_items.update');
    Route::put('/sponsor-items/{id}/inactive', [SponsorController::class, 'item_inactive'])->name('sponsor_items.inactive');
    Route::get('applicationView', [AdminController::class, 'applicationView'])->name('application.view');
    Route::put('/application/update/{id}', [AdminController::class, 'applicationUpdate'])->name('application.update');
    Route::post('verify-payment', [PaymentController::class, 'verifyPayment'])->name('verify.payment');
    Route::post('verify-extra-payment', [PaymentController::class, 'verifyExtraPayment'])->name('verify.extra-payment');
    Route::view('/sponsorship/list', 'sponsor.applications')->name('users.list');
    Route::get('/sponsors_list', [SponsorController::class, 'get_applications']);
    Route::post('approve-sponsorship', [SponsorController::class, 'approve'])->name('approve.sponsorship');
    Route::get('/sponsorship-list/', [AdminController::class, 'sponsorApplicationList'])->name('sponsorship.lists');
    Route::get('/sponsorship-list/{status}', [AdminController::class, 'sponsorApplicationList'])->name('sponsorship.list');
    Route::get('/invoice', [InvoicesController::class, 'index'])->name('invoice.list');
    Route::get('/invoice/{id}', [InvoicesController::class, 'show'])->name('invoice.show');
    Route::post('membership/verify', [AdminController::class, 'verifyMembership'])->name('membership.verify');
    Route::post('membership/reject', [AdminController::class, 'unverifyMembership'])->name('membership.reject');
    Route::get('export_users', [ExportController::class, 'export_users'])->name('export.users');
    Route::get('export_applications', [ExportController::class, 'export_applications'])->name('export.applications');
    Route::get('export_approved_applications', [ExportController::class, 'export_approved_applications'])->name('export.app.applications');
    Route::get('export_sponsorships', [ExportController::class, 'export_sponsorship_applications'])->name('export.sponsorships');
    Route::get('export_requirements', [ExportController::class, 'extra_requirements_export'])->name('export.requirements');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Role-based middleware groups
Route::middleware(['auth', 'role:sponsor'])->group(function () {
    Route::get('/sponsor/application', fn() => app(ApplicationController::class)->showForm('sponsor'))->name('application.sponsor');
    Route::post('/sponsor/application', [ApplicationController::class, 'submitForm'])->name('application.sponsor.submit');
    Route::get('/sponsor/dashboard', fn() => view('sponsor.dashboard'))->name('dashboard.sponsor');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard_old', [DashboardController::class, 'exhibitorDashboard'])->name('dashboard.admin');
    Route::get('/admin/dashboard', [DashboardController::class, 'exhibitorDashboard_new'])->name('dashboard.admin');
});

// Routes without middleware (misc)
Route::get('/import_states', [MisController::class, 'getCountryAndState']);
Route::post('/get-states', [MisController::class, 'getStates'])->name('get.states');
Route::get('extra_requirements', [ExtraRequirementController::class, 'index'])->name('extra_requirements.index');
Route::get('extra_requirements/list', [ExtraRequirementController::class, 'list'])->name('extra_requirements.list');
Route::get('requirements/order', [ExtraRequirementController::class, 'allOrders'])->name('extra_requirements.admin');
Route::get('/download-invoice', [InvoicesController::class, 'generatePDF'])->name('download.invoice');
Route::get('/invited/{token}/', [ExhibitorController::class, 'invited'])->name('exhibition.invited');
Route::get('/invited/', fn() => redirect('invited/not-found'))->name('exhibition.invited2');
Route::post('/invite/submit', [ExhibitorController::class, 'inviteeSubmitted'])->name('exhibition.invitee.submit');
Route::get('/badge/{applicationId}/{ticketType}', [AllocationController::class, 'readBadge'])->name('badge.read');
Route::get('/badge/{applicationId}/{ticketType}/edit', [AllocationController::class, 'editBadge'])->name('badge.edit');
Route::post('/badge/add', [AllocationController::class, 'addBadgeCategory'])->name('badge.add');
Route::get('/allocations', [AllocationController::class, 'showAllAllocations'])->name('allocations.list');
Route::get('/sponsor-items', [SponsorController::class, 'index']);
Route::get('/co-exhibitors', [CoExhibitorController::class, 'index'])->name('co_exhibitors')->middleware(Auth::class);
Route::post('/co-exhibitor/approve/{id}', [CoExhibitorController::class, 'approve'])->name('co_exhibitor.approve')->middleware(Auth::class);
Route::post('/co-exhibitor/reject/{id}', [CoExhibitorController::class, 'reject'])->name('co_exhibitor.reject')->middleware(Auth::class);
