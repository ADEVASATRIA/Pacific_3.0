<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Back\Clubhouse\ClubhouseController;
use App\Http\Controllers\Back\Coach\BackCoachController;
use App\Http\Controllers\Back\Promo\PromoController;
use App\Http\Controllers\Back\Staff\StaffController;
use App\Http\Controllers\Back\Tickets\PackageComboController;
use App\Http\Controllers\Back\Tickets\TicketTypeController;
use App\Http\Controllers\Back\Transaction\TransactionController;
use App\Http\Controllers\Back\View\DashboardController;
use App\Http\Controllers\Back\Member\MemberController as BackMemberController;
use App\Http\Controllers\Back\Customer\CustomerController as BackCustomerController;

use App\Http\Controllers\Front\Admin\AdminAuthController;
use App\Http\Controllers\Front\Admin\CashSessionController;
use App\Http\Controllers\Front\Admin\MemberViewController;
use App\Http\Controllers\Front\Admin\PackageViewController;
use App\Http\Controllers\Front\Admin\TransactionViewController;
use App\Http\Controllers\Front\Checkout\CheckoutController;
use App\Http\Controllers\Front\Coach\CoachController;
use App\Http\Controllers\Front\Customer\CheckCustomerController;
use App\Http\Controllers\Front\Customer\RegisterCustomerController;
use App\Http\Controllers\Front\Member\MemberController;
use App\Http\Controllers\Front\Member\PrintMemberViewController;
use App\Http\Controllers\Front\Package\PackageController;
use App\Http\Controllers\Front\View\CheckoutViewController;
use App\Http\Controllers\Front\View\CustomerController;
use App\Http\Controllers\Front\View\HomeController;
use App\Http\Controllers\Front\View\TiketController;
use Illuminate\Support\Facades\Route;

// Controller Back


// Login Route
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('login.do');
Route::post('/logout-fo', [AuthController::class, 'logoutFo'])->name('logout.fo');
Route::post('/logout-bo', [AuthController::class, 'logoutBo'])->name('logout.bo');


Route::middleware('fo.auth')->group(function () {
    Route::post('/cash/open', [CashSessionController::class, 'store'])->name('cash.store');
    Route::get('/cashsession/export', [CashSessionController::class, 'exportReport'])->name('cashsession.export');
    Route::post('/cashsession/close', [CashSessionController::class, 'processClose'])->name('cashsession.processClose');

    // Halaman utama FO
    Route::get('/', [HomeController::class, 'index'])->name('main');

    // Customer registration & check
    Route::get('/input-telephone', [CustomerController::class, 'inputTelephone'])->name('input_telephone');
    Route::get('/registrasi-new-customer', [CustomerController::class, 'registrasiNewCustomer'])->name('registrasi_new_customer');
    Route::post('/check-customer', [CheckCustomerController::class, 'checkCustomer'])->name('check_customer');
    Route::post('/register-data-customer', [RegisterCustomerController::class, 'registerDataCustomer'])->name('register_data_customer');

    // Ticket routes
    Route::get('/index-ticket', [TiketController::class, 'indexViewTicket'])->name('index_ticket');
    Route::get('/checkout-ticket', [CheckoutViewController::class, 'checkoutView'])->name('checkout_ticket');
    Route::post('/submitFormTicket', [CheckoutController::class, 'submitFormTicket'])->name('submit_form_ticket');

    // Payment
    Route::post('/do-checkout', [CheckoutController::class, 'doCheckout'])->name('do_checkout');
    Route::get("/checkout/success/{id}", [CheckoutController::class, 'checkoutSuccess'])->name('checkout_success');

    // Print ticket
    Route::get('/print-ticket/{purchaseID}', [CheckoutViewController::class, 'printTickets'])->name('print_ticket');

    // Admin (kasir level)
    Route::get('/admin', [AdminAuthController::class, 'index'])->name('admin.index');
    Route::post('/admin/check-pin', [AdminAuthController::class, 'checkPin'])->name('admin.check_pin');
    Route::get('/admin/transaction', [TransactionViewController::class, 'transactionIndex'])->name('admin.transaksi');
    Route::get('/admin/member', [MemberViewController::class, 'viewMemberIndex'])->name('admin.member');

    Route::get('/admin/close', [CashSessionController::class, 'close'])->name('admin.close');

    Route::get('/admin/package', [PackageViewController::class, 'index'])->name('admin.package');

    // Member print
    Route::get('/input-member', [MemberController::class, 'inputMember'])->name('input_member');
    Route::post('/check-member', [MemberController::class, 'checkMember'])->name('check_member');
    Route::get('/print-member/{customerID}', [PrintMemberViewController::class, 'printMember'])->name('member.print_member');
    Route::post('/member/extend', [MemberController::class, 'memberExtend'])->name('member.extend');
    Route::get('/member/list-ticket-member', [MemberController::class, 'indexExtendMember'])->name('member.list-ticket-member');
    Route::post('/submitFormMember', [CheckoutController::class, 'submitFormMember'])->name('submit_form_member');

    // Package Print 
    Route::get('/input-package', [PackageController::class, 'inputPackage'])->name('input_package');
    Route::post('/check-package', [PackageController::class, 'checkPackage'])->name('check_package');

    // Coach Print
    Route::get('/input-coach', [CoachController::class, 'inputCoach'])->name('input_coach');
    Route::post('/check-coach', [CoachController::class, 'checkCoach'])->name('check_coach');
});

Route::middleware('bo.auth')->group(function () {
    // Halaman utama BO
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route Transaction View
    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction');
    Route::get('/transaction/detail/{id}', [TransactionController::class, 'detail'])->name('transaction.detail');

    //Route Promo Management view back office
    Route::get('/promo', [PromoController::class, 'index'])->name('promo');
    Route::get('/get-promo/{id}', [PromoController::class, 'getPromo']);
    Route::post('/do-create-promo', [PromoController::class, 'add'])->name('add.promo');
    Route::post('/edit-promo/{id}', [PromoController::class, 'edit'])->name('edit.promo');
    Route::delete('/delete-promo/{id}', [PromoController::class, 'delete'])->name('delete.promos');


    // Route Ticket Types view back office
    Route::get('/ticket-types', [TicketTypeController::class, 'index'])->name('ticket-types');
    Route::get('/get-ticket-types/{id}', [TicketTypeController::class, 'getTicketTypes']);
    Route::post('/do-create-ticket-type', [TicketTypeController::class, 'add'])->name('add.ticket_types');
    Route::post('/edit-ticket-type/{id}', [TicketTypeController::class, 'edit'])->name('edit.ticket_types');
    Route::delete('/delete-ticket-type/{id}', [TicketTypeController::class, 'delete'])->name('delete.ticket_types');

    //Route Package Combo view Back office
    Route::get('/package-combo', [PackageComboController::class, 'index'])->name('package-combo');
    Route::get('/get-package-combo/{id}', [PackageComboController::class, 'getPackageCombo'])->name('get.package-combo');
    Route::post('/add-package-combo', [PackageComboController::class, 'add'])->name('add.package-combo');
    Route::post('/edit-package-combo/{id}', [PackageComboController::class, 'edit'])->name('edit.package-combo');
    Route::delete('/delete-package-combo/{id}', [PackageComboController::class, 'delete'])->name('delete.package-combo');

    // Routes for management staff
    Route::get('/staff', [StaffController::class, 'index'])->name('staff');
    Route::get('/get-staff/{id}', [StaffController::class, 'getAdmin']);
    Route::post('/add-staff', [StaffController::class, 'add'])->name('add.staff');
    Route::post('/edit-staff/{id}', [StaffController::class, 'edit'])->name('edit.staff');
    Route::delete('/delete-staff/{id}', [StaffController::class, 'delete'])->name('delete.staff');

    // Routes for management member
    Route::get('/member', [BackMemberController::class, 'index'])->name('member');
    Route::get('/get-member/{id}', [BackMemberController::class, 'getMember']);
    Route::post('/edit-member/{id}', [BackMemberController::class, 'edit'])->name('edit.member');
    Route::delete('/delete-member/{id}', [BackMemberController::class, 'delete'])->name('delete.member');

    // Routes for management coach
    Route::get('/coach', [BackCoachController::class, 'index'])->name('coach');
    Route::get('/get-coach/{id}', [BackCoachController::class, 'getCoach']);
    Route::post('/add-coach', [BackCoachController::class, 'add'])->name('add.coach');
    Route::post('/edit-coach/{id}', [BackCoachController::class, 'edit'])->name('edit.coach');
    Route::delete('/delete-coach/{id}', [BackCoachController::class, 'delete'])->name('delete.coach');

    // Routes for management Clubhouse
    Route::get('/clubhouse', [ClubhouseController::class, 'index'])->name('clubhouse');
    Route::get('/get-clubhouse/{id}', [ClubhouseController::class, 'getClubhouse']);
    Route::post('/add-clubhouse', [ClubhouseController::class, 'add'])->name('add.clubhouse');
    Route::post('/edit-clubhouse/{id}', [ClubhouseController::class, 'edit'])->name('edit.clubhouse');
    Route::delete('/delete-clubhouse/{id}', [ClubhouseController::class, 'delete'])->name('delete.clubhouse');


    // Routes For Management data customer
    Route::get('/customer', [BackCustomerController::class, 'index'])->name('customer');
    Route::get('/get-customer/{id}', [BackCustomerController::class, 'getCustomer']);
    Route::post('/add-customer', [BackCustomerController::class, 'add'])->name('add.customer');
    Route::post('/edit-customer/{id}', [BackCustomerController::class, 'edit'])->name('edit.customer');
    Route::delete('/delete-customer/{id}', [BackCustomerController::class, 'delete'])->name('delete.customer');


    
});

