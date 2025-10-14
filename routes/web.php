<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Back\Promo\PromoController;
use App\Http\Controllers\Back\Tickets\TicketTypeController;
use App\Http\Controllers\Back\Transaction\TransactionController;
use App\Http\Controllers\Back\View\DashboardController;
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
    Route::post('/do-create-ticket-type', [TicketTypeController::class, 'add'])->name('add.ticket_types');
    Route::delete('/delete-ticket-type/{id}', [TicketTypeController::class, 'delete'])->name('delete.ticket_types');
});

