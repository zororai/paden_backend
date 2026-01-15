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
    EmailVerificationController,
    SmsVerificationController
};
use App\Http\Controllers\Api\General\{
    GeneralRegisterController,
    GeneralPropertyController,
    GeneralLandlordController,
    GeneralEnquiryController,
    GeneralNotificationController,
    GeneralAdminController
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
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
Route::post('/useregister', [RegisterController::class, 'register']);
Route::post('password/email', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [PasswordResetController::class, 'reset']);

// ✅ SMS Verification Routes
Route::post('/login/request-code', [SmsVerificationController::class, 'requestCode'])->middleware('throttle:5,1');
Route::post('/login/verify-code', [SmsVerificationController::class, 'verifyCode'])->middleware('throttle:10,1');
Route::post('/login/resend-code', [SmsVerificationController::class, 'resendCode'])->middleware('throttle:3,1');

// ✅ Device-based Auth Routes (protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [LoginController::class, 'me']);
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/logout-all', [LoginController::class, 'logoutAll']);
    Route::get('/devices', [LoginController::class, 'devices']);
    Route::delete('/devices/{deviceId}', [LoginController::class, 'logoutDevice']);
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
    Route::get('property/{id}/rating-summary', [DisplayReviewController::class, 'getRatingSummary']);


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
    Route::get('/chat/received', [ChatController::class, 'getReceivedMessages']);

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

/*
|--------------------------------------------------------------------------
| General Housing Routes
|--------------------------------------------------------------------------
*/

// ✅ General Housing Registration (Public)
Route::post('/register/general', [GeneralRegisterController::class, 'register']);

// ✅ General Housing - Tenant Routes
Route::middleware(['auth:sanctum', 'role:tenant', 'housing.context:general'])->prefix('general')->group(function () {
    Route::get('/properties', [GeneralPropertyController::class, 'index']);
    Route::get('/properties/{id}', [GeneralPropertyController::class, 'show']);
    Route::post('/enquiries', [GeneralEnquiryController::class, 'store']);
    Route::get('/notifications', [GeneralNotificationController::class, 'index']);
    Route::post('/notifications/mark-read', [GeneralNotificationController::class, 'markAllRead']);
});

// ✅ General Housing - Landlord Routes
Route::middleware(['auth:sanctum', 'role:landlord', 'housing.context:general'])->prefix('general/landlord')->group(function () {
    // Profile routes (no profile completion required)
    Route::get('/profile/status', [GeneralLandlordController::class, 'profileStatus']);
    Route::post('/profile', [GeneralLandlordController::class, 'updateProfile']);
    
    // Property routes (profile completion required)
    Route::middleware('profile.complete')->group(function () {
        Route::get('/properties', [GeneralLandlordController::class, 'getProperties']);
        Route::post('/properties', [GeneralLandlordController::class, 'store']);
        Route::put('/properties/{id}', [GeneralLandlordController::class, 'update']);
        Route::delete('/properties/{id}', [GeneralLandlordController::class, 'destroy']);
    });
});

// ✅ General Housing - Admin Routes
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin/general')->group(function () {
    Route::get('/properties', [GeneralAdminController::class, 'getProperties']);
    Route::get('/users', [GeneralAdminController::class, 'getUsers']);
    Route::patch('/property/{id}/status', [GeneralAdminController::class, 'updatePropertyStatus']);
});


