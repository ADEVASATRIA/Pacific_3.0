<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Back\View\DashboardController;
use App\Http\Controllers\Front\Admin\AdminAuthController;
use App\Http\Controllers\Front\Admin\MemberViewController;
use App\Http\Controllers\Front\Admin\TransactionViewController;
use App\Http\Controllers\Front\Checkout\CheckoutController;
use App\Http\Controllers\Front\Customer\CheckCustomerController;
use App\Http\Controllers\Front\Customer\RegisterCustomerController;
use App\Http\Controllers\Front\Member\MemberController;
use App\Http\Controllers\Front\Member\PrintMemberViewController;
use App\Http\Controllers\Front\View\CheckoutViewController;
use App\Http\Controllers\Front\View\CustomerController;
use App\Http\Controllers\Front\View\HomeController;
use App\Http\Controllers\Front\View\TiketController;
use Illuminate\Support\Facades\Route;

// Login Route
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('login.do');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('fo.auth')->group(function () {

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

    // Member print
    Route::get('/input-member', [MemberController::class, 'inputMember'])->name('input_member');
    Route::post('/check-member', [MemberController::class, 'checkMember'])->name('check_member');
    Route::get('/print-member/{customerID}', [PrintMemberViewController::class, 'printMember'])->name('member.print_member');
    Route::post('/member/extend', [MemberController::class, 'memberExtend'])->name('member.extend');
    Route::get('/member/list-ticket-member', [MemberController::class, 'indexExtendMember'])->name('member.list-ticket-member');
    Route::post('/submitFormMember', [CheckoutController::class, 'submitFormMember'])->name('submit_form_member');
});

Route::middleware('bo.auth')->group(function () {
    // Halaman utama BO
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

