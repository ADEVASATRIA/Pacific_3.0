<?php

use App\Http\Controllers\API\Back\Report\ReportVisitorController;
use App\Http\Controllers\API\Bug\MemberController as APIMemberController;
use App\Http\Controllers\API\Front\Customer\CustomerApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/member-active', [APIMemberController::class, 'getDataMember']);
Route::post('/non-active-members', [APIMemberController::class, 'nonActiveMemberStatusTicket']);
Route::post('/repair-active-member', [APIMemberController::class, 'repairActiveMember']);
Route::post('/repair-all-active-member', [APIMemberController::class, 'repairAllActiveMembers']);


Route::prefix('customer')->group(function () {
    Route::get('/all', [CustomerApiController::class, 'getAllCustomers']);
    Route::get('/search-by-phone', [CustomerApiController::class, 'searchByPhone']);
});


Route::prefix('report')->group(function () {
    Route::get('/back/grouping-ticket-regular', [ReportVisitorController::class, 'groupingTicketRegular']);
    Route::get('/back/grouping-ticket-pengantar', [ReportVisitorController::class, 'groupingTicketPengantar']);
    Route::get('/back/grouping-ticket-coach', [ReportVisitorController::class, 'groupingTicketCoach']);
    Route::get('/back/grouping-ticket-member', [ReportVisitorController::class, 'groupingTicketMember']);
});