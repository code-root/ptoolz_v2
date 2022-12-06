<?php

use App\Http\Controllers\customer\deliveryController;
use App\Http\Controllers\customer\orderController;
use App\Http\Controllers\customer\orderDetailsController;
use App\Http\Controllers\customer\orderTransactionsController;
use App\Http\Controllers\customer\updatesController as CustomerUpdatesController;
use App\Http\Controllers\gloabalDetailsController;
use App\Http\Controllers\payment\paymentController;
use App\Http\Controllers\registration\dataController;
use App\Http\Controllers\registration\userController;
use App\Http\Controllers\sp\offerController;
use App\Http\Controllers\sp\profileController;
use App\Http\Controllers\sp\referenceController;
use App\Http\Controllers\sp\updatesController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// registration
Route::post('sign_up'  , [userController::class , 'signup']);
Route::post('login'  , [userController::class , 'login']);
Route::get('category', [dataController::class ,'catgeory']);
Route::post('send/{type}' , [userController::class , 'sendCode']);
Route::post('check/{type}' , [userController::class , 'checkCode']);
Route::post('change_password' , [userController::class , 'change_password']);
Route::post('login_mobile/send' , [userController::class , 'sendMobile']);
Route::post('login_mobile' , [userController::class , 'mobileLogin']);

// orders details
Route::middleware('auth:sanctum')->group(function(){
Route::get('paper_size' , [orderDetailsController::class , 'papersize']);
Route::get('paper_type' , [orderDetailsController::class , 'papertype']);
Route::get('paper_binding/{paper_size}' , [orderDetailsController::class , 'paperBinding']);
Route::get('printing_color' , [orderDetailsController::class , 'printingColor']);
Route::get('printing_side' , [orderDetailsController::class , 'printingSide']);
Route::get('picture_size' , [orderDetailsController::class , 'picturesize']);
Route::get('camera_accessories/{camera}' , [orderDetailsController::class , 'camera_accessories']);
Route::get('occasion' , [orderDetailsController::class , 'occassion']);
Route::get('camera' , [orderDetailsController::class , 'camera']);
Route::get('country' , [orderDetailsController::class , 'country']);
Route::get('city/{country}' , [orderDetailsController::class , 'city']);
Route::get('region/{city}' , [orderDetailsController::class , 'region']);
Route::get('country_universities/{country}' , [gloabalDetailsController::class , 'country_universities']);
Route::get('colleges/{university}' , [gloabalDetailsController::class , 'colleges']);
Route::get('departments/{college}' , [gloabalDetailsController::class , 'departments']);
Route::get('semesters' , [gloabalDetailsController::class , 'semesters']);
Route::get('collegeyears' , [gloabalDetailsController::class , 'collegeyears']);
Route::post('instructors' , [gloabalDetailsController::class , 'instructors_filter']);
Route::post('references' , [gloabalDetailsController::class , 'references_filter']);
Route::get('fees' , [gloabalDetailsController::class , 'System_fees']);
Route::get('logout' , [userController::class , 'logout']);
Route::get('user_profie' , [gloabalDetailsController::class , 'user_profie']);

});



// cusromer routes
Route::middleware('auth:sanctum')->prefix('c')->group(function(){
 Route::resource('order', orderController::class);
 Route::post('order/update/{order}', [orderController::class , 'update']);
 Route::get('offers/{order}', [orderController::class , 'offers']);
 Route::get('my_orders', [orderController::class ,'my_orders']);
 Route::post('accept_and_hold/{offer}', [orderTransactionsController::class ,'accept_and_hold']);
Route::get('payment/{offer}' , [paymentController::class , 'index']);
Route::get('invoice/{offer}' , [orderController::class , 'invoice']);
Route::resource('updates' , CustomerUpdatesController::class);
Route::post('updates/{update}' , [CustomerUpdatesController::class , 'update']);
Route::post('accept_update/{update}' , [CustomerUpdatesController::class , 'accept_update']);
Route::post('order_delete/{order}' ,[orderController::class , 'destroy'] );
Route::post('offer_cancel/{offer}' ,[orderController::class , 'cancel_offer'] );
Route::resource('delivery', deliveryController::class);

});


// sp routes
Route::middleware('auth:sanctum')->prefix('s')->group(function(){
 Route::get('orders', [offerController::class , 'index']);

 Route::resource('reference', referenceController::class);
 Route::resource('offer', offerController::class);
 Route::get('academic_permission', [referenceController::class , 'academic_permission']);
 Route::get('printer_instructors', [referenceController::class , 'printer_instructors']);

Route::resource('update', updatesController::class);
Route::get('order/{order}', [offerController::class , 'order_details']);
Route::get('my_offers' ,[offerController::class , 'my_offers']);
Route::post('reference_delete/{reference}' ,[referenceController::class , 'destroy'] );
Route::resource('profile' , profileController::class);
Route::post('add_experience'  , [profileController::class , 'add_experience']);
Route::post('add_portfolio'  , [profileController::class , 'add_portfolio']);
});
Route::post('payment/proccess' , [paymentController::class , 'proccess']);
Route::get('qrcodes' , [orderController::class, 'qrcodes']);
