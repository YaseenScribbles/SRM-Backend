<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('users', UserController::class);
    Route::apiResource('purposes', PurposeController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('visits', VisitController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('distributors', DistributorController::class);

    //options
    Route::get('/managers', [HelperController::class, 'managers']);
    Route::get('/states', [HelperController::class, 'states']);
    Route::get('/brands', [HelperController::class, 'brands']);
    Route::get('/styles', [HelperController::class, 'styles']);
    Route::get('/sizes', [HelperController::class, 'sizes']);
    Route::get('/order-pdf/{orderId}', [HelperController::class, 'order_pdf']);
    Route::post('/email-pdf', [HelperController::class, 'sendMailOrderPDF']);
    Route::get('/user-role-users', [HelperController::class, 'userRoleUsers']);
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('/user-rights/{id}',[HelperController::class, 'userRights']);
    Route::put('/user-rights/{id}', [HelperController::class, 'updateUserRights']);
    Route::get('/menus', [HelperController::class,'menus']);
});
