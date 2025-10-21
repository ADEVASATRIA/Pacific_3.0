<?php

use App\Http\Controllers\API\Bug\MemberController as APIMemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/member-active', [APIMemberController::class, 'getDataMember']);
Route::post('/non-active-members', [APIMemberController::class, 'nonActiveMemberStatusTicket']);
Route::post('/repair-active-member', [APIMemberController::class, 'repairActiveMember']);
Route::post('/repair-all-active-member', [APIMemberController::class, 'repairAllActiveMembers']);


