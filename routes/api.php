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
    DirectionController,
    ChatController,
    SmsController,
    RoomShareRequestController
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
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
    Route::patch('/properties/{id}/roomnumber', [PropertyController::class, 'updateRoomNumber']);
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
    Route::get('/user/{id}', [ProfileController::class, 'getUserById']);
    Route::get('/properties/bylocation', [SearchController::class, 'getPropertiesByUniversity']);
    Route::post('/search', [SearchController::class, 'search']);

    // ✅ Chat Routes
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/messages/{userId}', [ChatController::class, 'getMessages']);
    Route::get('/chat/conversations', [ChatController::class, 'getConversations']);
    Route::post('/chat/mark-read/{userId}', [ChatController::class, 'markAsRead']);

    // ✅ SMS Routes
    Route::post('/sms/send/{userId}', [SmsController::class, 'sendSms']);

    // ✅ Room Share Request Routes
    Route::post('/room-share/send', [RoomShareRequestController::class, 'sendRequest']);
    Route::get('/room-share/sent', [RoomShareRequestController::class, 'getSentRequests']);
    Route::get('/room-share/university', [RoomShareRequestController::class, 'getUniversityPosts']);
    Route::put('/room-share/accept/{id}', [RoomShareRequestController::class, 'acceptRequest']);
    Route::put('/room-share/reject/{id}', [RoomShareRequestController::class, 'rejectRequest']);
    Route::delete('/room-share/{id}', [RoomShareRequestController::class, 'deleteRequest']);
    Route::get('/room-share/student/{id}', [RoomShareRequestController::class, 'getStudentProfile']);
    Route::get('/room-share/students', [RoomShareRequestController::class, 'getStudents']);

});


