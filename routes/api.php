<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProjectBidController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// Protected the routes by sanctum through this group.
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/user', function () {
        return auth()->user();
    });

    Route::post('/store_project', [ProjectController::class, 'store'])->name('project.store');
    Route::delete('/project/{project}', [ProjectController::class, 'destroy'])->name('project.destroy');

    Route::post('/store_project_category', [ProjectCategoryController::class, 'store']);
    Route::post('/store_project_bid', [ProjectBidController::class, 'store']);

    Route::post('/project_bids/{project}', [ProjectBidController::class, 'projectBids']);

    Route::post('/orders', [OrderController::class, 'workerOrders']);

    Route::post('/store_order', [OrderController::class, 'store']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/projects', [ProjectController::class, 'index'])->name('projects');
Route::post('/project/{project}', [ProjectController::class, 'singleProject'])->name('project');

Route::post('/order/{order}', [OrderController::class, 'singleOrder']);
