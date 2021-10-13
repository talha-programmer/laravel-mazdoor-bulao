<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\JobBidController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\UserController;
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
        $response = [
            'user' => auth()->user()
        ];
        return response($response, '200');
    });

    Route::post('/user/applied_jobs', [UserController::class, 'appliedJobs']);
    Route::post('/user/posted_jobs', [UserController::class, 'postedJobs']);
    Route::post('/user/bids', [UserController::class, 'userBids']);

    Route::post('/store_job', [JobController::class, 'store'])->name('job.store');
    Route::delete('/destroy_job', [JobController::class, 'destroy'])->name('job.destroy');

    Route::post('/store_job_category', [JobCategoryController::class, 'store']);
    Route::post('/store_job_bid', [JobBidController::class, 'store']);
    Route::delete('/destroy_job_bid', [JobBidController::class, 'destroy']);

    Route::post('/job_bids/{job}', [JobBidController::class, 'jobBids']);

    Route::post('/selling_orders', [OrderController::class, 'sellingOrders']);
    Route::post('/buying_orders', [OrderController::class, 'buyingOrders']);

    Route::post('/start_order', [OrderController::class, 'startOrder']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/jobs', [JobController::class, 'index'])->name('jobs');
Route::post('/jobs/{job}', [JobController::class, 'singleJob'])->name('job');

Route::post('/job_categories', [JobCategoryController::class, 'index']);

Route::post('/order/{order}', [OrderController::class, 'singleOrder']);
