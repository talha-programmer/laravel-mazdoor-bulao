<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\JobBidController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerProfileController;
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

    Route::post('/user/store_worker_profile', [WorkerProfileController::class, 'store']);
    Route::post('/user/worker_profile', [WorkerProfileController::class, 'index']);

    //Route::post('/worker_skills', [SkillController::class, 'index']);

    Route::post('/chat_users', [UserController::class, 'chatUsers']);

    Route::post('/chat/add_in_chat', [ChatController::class, 'addInChat']);
    Route::post('/chat/chat_with/{user:id}', [ChatController::class, 'chatWithUser']);

    Route::post('/chat/send_message', [ChatController::class, 'sendMessage']);

    Route::post('/store_job', [JobController::class, 'store'])->name('job.store');
    Route::delete('/destroy_job', [JobController::class, 'destroy'])->name('job.destroy');

    Route::post('/store_job_category', [JobCategoryController::class, 'store']);
    Route::post('/store_job_bid', [JobBidController::class, 'store']);
    Route::delete('/destroy_job_bid', [JobBidController::class, 'destroy']);

    Route::post('/job_bids/{job}', [JobBidController::class, 'jobBids']);

    Route::post('/selling_orders', [OrderController::class, 'sellingOrders']);
    Route::post('/buying_orders', [OrderController::class, 'buyingOrders']);

    Route::post('/start_order', [OrderController::class, 'startOrder']);
    Route::post('/complete_buying_order', [OrderController::class, 'completeOrder']);
    Route::post('/complete_selling_order', [OrderController::class, 'completionRequest']);

    Route::post('/send_review', [ReviewController::class, 'store']);
    Route::post('/order_reviews', [ReviewController::class, 'getOrderReviews']);

    Route::post('/worker_reviews_given', [WorkerProfileController::class, 'getReviewsGiven']);
    Route::post('/worker_reviews_received', [WorkerProfileController::class, 'getReviewsReceived']);

    Route::post('/buyer_reviews_given', [BuyerProfileController::class, 'getReviewsGiven']);
    Route::post('/buyer_reviews_received', [BuyerProfileController::class, 'getReviewsReceived']);

    Route::post('/order/{order}', [OrderController::class, 'singleOrder']);

    Route::post('/jobs', [JobController::class, 'jobsWithSkills'])->name('jobs');

});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/jobs/{job}', [JobController::class, 'singleJob'])->name('job');

Route::post('/job_categories', [JobCategoryController::class, 'index']);

