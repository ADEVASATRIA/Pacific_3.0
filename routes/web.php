<?php

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

Route::get('/', [HomeController::class, 'index'])->name('main');
Route::get('/input-telephone', [CustomerController::class, 'inputTelephone'])->name('input_telephone');
Route::get('/registrasi-new-customer', [CustomerController::class, 'registrasiNewCustomer'])->name('registrasi_new_customer');
Route::post('/check-customer', [CheckCustomerController::class, 'checkCustomer'])->name('check_customer');
Route::post('/register-data-customer', [RegisterCustomerController::class, 'registerDataCustomer'])->name('register_data_customer');

// Route Ticket View
Route::get('/index-ticket', [TiketController::class, 'indexViewTicket'])->name('index_ticket');
Route::get('/checkout-ticket', [CheckoutViewController::class, 'checkoutView'])->name('checkout_ticket');
Route::post('/submitFormTicket', [CheckoutController::class, 'submitFormTicket'])->name('submit_form_ticket');

// Route Payment 1.0
Route::post('/do-checkout', [CheckoutController::class, 'doCheckout'])->name('do_checkout');
Route::get("/checkout/success/{id}", [CheckoutController::class, 'checkoutSuccess'])->name('checkout_success');

// Route Print Ticket Regular 
Route::get('/print-ticket/{purchaseID}', [CheckoutViewController::class, 'printTickets'])->name('print_ticket');


// Admin Routes
Route::get('/admin', [AdminAuthController::class, 'index'])->name('admin.index');
Route::post('/admin/check-pin', [AdminAuthController::class, 'checkPin'])->name('admin.check_pin');
Route::get('/admin/transaction', [TransactionViewController::class, 'transactionIndex'])->name('admin.transaksi');
Route::get('/admin/member', [MemberViewController::class, 'viewMemberIndex'])->name('admin.member');

// Route Print member
Route::get('/input-member', [MemberController::class, 'inputMember'])->name('input_member');
Route::post('/check-member', [MemberController::class, 'checkMember'])->name('check_member');
Route::get('/print-member/{customerID}', [PrintMemberViewController::class, 'printMember'])->name('member.print_member');