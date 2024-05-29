<?php

use App\Http\Controllers\BuyingOrderController;
use App\Http\Controllers\CodeCheckController;
use App\Http\Controllers\CustomerControler;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\FollowUpRequestController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\MCategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Traits\Notifications;
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

Route::get('test',function (){
    $id[]=[1];
    $title='notification';
    $message= 'notification sent successfully' ;
    $type='user';

   return response()->json(Notifications::notify($id,$title,$message,$type));
//   return response()->json('done');
});

Route::prefix('Customer')->group(function () {

    Route::post('/signUp', [CustomerControler::class, 'signUp']);

    Route::post('/login', [CustomerControler::class, 'login']);

    Route::middleware(['auth:api'])->group(function () {

        Route::post("/RequestCode", [CodeCheckController::class, "RequestCode"]);
        Route::post("/CheckCode", [CodeCheckController::class, "checkCode"]);
        Route::post('/verifyAccount', [CustomerControler::class, 'verifyAccount']);
        Route::put("/ProfileUpdate", [CustomerControler::class, "ProfileUpdate"]);
        Route::get("/MyProfile", [CustomerControler::class, "MyProfile"]);
        Route::post('/ResetPassword', [CustomerControler::class, 'ResetPassword']);
        Route::post('refreshToken',[CustomerControler::class,'refreshToken']);
        Route::get('/logout', [CustomerControler::class, 'logout']);


        //Role
        Route::middleware(['role:customer'])->group(function () {
            //Post
            Route::get('post/{post}', [PostController::class, 'show']);
            Route::get('post', [PostController::class, 'index']);
            Route::get('ListMostLikedPosts', [PostController::class, 'ListMostLikedPosts']);


            //like
            Route::post('/like/{post}', [LikeController::class, 'addLike']);
            Route::delete('/like/{post}', [LikeController::class, 'deleteLike']);

            // follow
            Route::post('/send', [FollowUpRequestController::class, 'followUpRequset']);
            Route::get('/cancel/{followUpRequest}', [FollowUpRequestController::class, 'cancelfollowUpRequset']);
            Route::get('/following', [FollowUpRequestController::class, 'showFollowing']);

            //market and its products
            Route::get('/showAllMarket', [MarketController::class, 'showAllMarket']);
            Route::get('/showMarkets/{m_category}', [MarketController::class, 'index']);
            Route::get('/showProductOfMarket/{market}', [MarketController::class, 'showProductOfMarket']);
            Route::get('getPosition/{market}',[MarketController::class,'getPosition']);
            //هي ممكن نعدل عليها لنجيب الاكثر مبيعاً


            //order
            Route::post('/order/{market}', [BuyingOrderController::class, 'sendOrder']);
            Route::delete('/order/cancel/{buyingOrder}', [BuyingOrderController::class, 'cancelOrder']);
            Route::get('/order/allOrder', [BuyingOrderController::class, 'showMyOrders']);
            Route::get('/order/acceptedOrder', [BuyingOrderController::class, 'showMyAcceptedOrder']);
            Route::get('/order/RejectedOrder', [BuyingOrderController::class, 'showMyRejectedOrder']);
            Route::get('/order/WaitingOrder', [BuyingOrderController::class, 'showMyWaitingOrder']);


            //chat
            Route::post('sendMessage',[MessageController::class,'store']);
            Route::get('show/{id}',[MessageController::class,'show']);
            Route::get('show_my_conversations',[MessageController::class,'show_my_conversations']);

            //notification
            Route::get('/getNotifications',[CustomerControler::class,'getNotifications']);


        });

        Route::get('getAllCategories',[MCategoryController::class,'getAll']);

    });
});


//________________________________________________________________________________________

Route::prefix('Market')->group(function () {

    Route::post('/signUp', [MarketController::class, 'signUp']);
    Route::post('/login', [MarketController::class, 'marketLogin']);

    Route::middleware(['auth:market-api'])->group(function () {
        Route::post("/RequestCode", [CodeCheckController::class, "RequestCode"]);
        Route::post("/CheckCode", [CodeCheckController::class, "checkCode"]);
        Route::post('/verifyAccount', [MarketController::class, 'verifyAccount']);
        Route::put("/ProfileUpdate", [MarketController::class, "ProfileUpdate"]);
        Route::get("/MyProfile", [MarketController::class, "MyProfile"]);
        Route::post('/ResetPassword', [MarketController::class, 'ResetPassword']);
        Route::post('refreshToken',[MarketController::class,'refreshToken']);
        Route::get('/logout', [MarketController::class, 'logout']);




        Route::middleware(['role:market'])->group(function () {

            //Post
            Route::post('/post', [PostController::class, 'store']);
            Route::put('post/{post}', [PostController::class, 'update']);
            Route::delete('post/{post}', [PostController::class, 'destroy']);
            Route::get('post/getMine', [PostController::class, 'ListMyPosts']);
            Route::get('post/{post}', [PostController::class, 'show']);

            //product
            Route::post('/upload', [ProductController::class, 'import']);
            Route::get('/show', [ProductController::class, 'showMyProducts']);
            Route::put('/edit', [ProductController::class, 'editMyProducts']);
            Route::delete('/deleteProduct/{product}', [ProductController::class, 'destroy']);
            Route::post('/addProduct', [ProductController::class, 'addProduct']);

            // follow request
            Route::get('/followers', [FollowUpRequestController::class, 'showMyFollowers']);
            Route::get('/request', [FollowUpRequestController::class, 'showMyFollowRequest']);
            Route::post('/accept/{followUpRequest}', [FollowUpRequestController::class, 'acceptFollowUpRequset']);
            Route::post('/reject/{followUpRequest}', [FollowUpRequestController::class, 'rejectFollowUpRequset']);

            //delivery
            Route::prefix('/delivery')->group(function () {
                Route::post('/', [DeliveryController::class, 'store']);
                Route::put('/{delivery}', [DeliveryController::class, 'update']);
                Route::delete('/{delivery}', [DeliveryController::class, 'destroy']);
                Route::get('/{delivery}', [DeliveryController::class, 'show']);
            });

            //order
            Route::post('/acceptOrder/{buyingOrder}', [BuyingOrderController::class, 'acceptOrder']);
            Route::post('/rejectOrder/{buyingOrder}', [BuyingOrderController::class, 'rejectOrder']);
            Route::get('/order/allOrder', [BuyingOrderController::class, 'showTheOrders']);
            Route::get('/order/showaccepted', [BuyingOrderController::class, 'showTheAcceptedOrder']);
            Route::get('/order/showRejectedOrder', [BuyingOrderController::class, 'showTheRejectedOrder']);
            Route::get('/order/showWaitingOrder', [BuyingOrderController::class, 'showTheWaitingOrder']);
            Route::get('/order/ReceiptConfirmation/{buyingOrder}', [BuyingOrderController::class, 'MarketReceiptConfirmation']);
            Route::get('/order/showDeliveredOrder', [BuyingOrderController::class, 'MarketshowDeliveredOrder']);

            //chat
            Route::post('send',[MessageController::class,'marketSend']);
            Route::get('show/{id}',[MessageController::class,'marketShow']);
            Route::get('show_my_conversations',[MessageController::class,'market_show_my_conversations']);

            //notification
            Route::get('/getNotifications',[MarketController::class,'getNotifications']);

        });

    });

});


//________________________________________________________________________________________
//delivery

 Route::prefix('Delivery')->group(function () {

    Route::post('/login', [DeliveryController::class, 'login']);


    Route::middleware(['auth:delivery-api'])->group(function () {

        Route::get('/logout', [DeliveryController::class, 'logout']);

        Route::get('/order/WaitToDeliverOrder', [BuyingOrderController::class, 'WaitToDeliverOrder']);

        Route::get('/order/showDeliveredOrder', [BuyingOrderController::class, 'showDeliveredOrder']);

        Route::get('/order/ReceiptConfirmation/{buyingOrder}', [BuyingOrderController::class, 'DeliveryReceiptConfirmation']);

    });

 });
