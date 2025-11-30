<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    PaymentController,
    PropertyController,
    HomeController,
    SearchController,
    UpdateUserController,
    UniversityController,
    ProfileController,
    DisplayReviewController,
    DirectionPaymentController,
    DirectionController

};
use App\Http\Controllers\Api\Auth\{
    RegisterController,
    PasswordResetController,
    LoginController,
    SocialAuthController,
    EmailVerificationController
};
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ✅ Authenticated User
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ Auth Routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/useregister', [RegisterController::class, 'register']);
Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [PasswordResetController::class, 'reset']);

// ✅ Email Verification Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/verify', [EmailVerificationController::class, 'verify']);
    Route::post('/email/resend', [EmailVerificationController::class, 'resend']);
});

// ✅ Social Login (Google)
Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
});
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);


Route::middleware('auth:sanctum')->get('/homedisplay/{id}', [HomeController::class, 'displa']);


Route::middleware('auth:sanctum')->post('payment/regpayment', [PaymentController::class, 'storepay']);

// ✅ Protected Routes (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // University
    Route::get('/my-university', [UniversityController::class, 'getMyUniversity']);

    // Profile & Upload
    Route::post('/upload', [UpdateUserController::class, 'store']);
    Route::post('/profile/update', [UpdateUserController::class, 'updateProfile']);


    // Payment
    //Route::post('/pay/regpay', [PaymentController::class, 'storepay']);
    Route::post('/directions/pay/{id}', [DirectionPaymentController::class, 'pay']);
    Route::get('/directions/payment/check/{id}', [DirectionPaymentController::class, 'checkPayment']);
  

    // Properties
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::get('/myproperties', [PropertyController::class, 'myProperties']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::put('/properties/update-landlord-agent/{id}/', [PropertyController::class, 'updateLandlordAgent']);
    Route::get('/home/popular', [HomeController::class, 'getPopularPropertyIds']);
        Route::get('home/gethome', [HomeController::class, 'gethome']);
   

    Route::get('reviews/{id}', [DisplayReviewController::class, 'index'])->name('reviews.index');
   
    Route::delete('reviews/delete/{reviewId}', [DisplayReviewController::class, 'deleteReviewById'])->name('reviews.delete.id');
    Route::post('reviews/store', [DisplayReviewController::class, 'storeReviews'])->name('reviews.store');
    Route::get('property/{id}/reviews', [DisplayReviewController::class, 'getReviewsForProperty'])->name('property.reviews');


    // Home
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/homediplay/{id}', [HomeController::class, 'display']);
    Route::post('/home/like', [HomeController::class, 'like']);

    Route::get('/properties/directions/{id}', [DirectionController::class, 'show']);
    

    // ✅ Public Routes
 


    Route::get('/profile', [ProfileController::class, 'showProfile']);
    Route::get('/properties/bylocation', [SearchController::class, 'getPropertiesByUniversity']);   
    Route::post('/search', [SearchController::class, 'search']);

});


