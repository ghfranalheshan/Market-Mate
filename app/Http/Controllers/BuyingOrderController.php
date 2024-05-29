<?php

namespace App\Http\Controllers;

use App\Http\Traits\Notifications;
use App\Models\BuyingOrder;
use App\Http\Requests\StoreBuyingOrderRequest;
use App\Http\Requests\UpdateBuyingOrderRequest;
use App\Models\Delivery;
use App\Models\FollowUpRequest;
use App\Models\Market;
use App\Models\Notification;
use App\Models\WithDelivery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BuyingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function sendOrder(StoreBuyingOrderRequest $request, Market $market): \Illuminate\Http\JsonResponse
    {
        $request_status = FollowupRequest::query()
            ->where('user_id', '=', Auth::guard('api')->id())
            ->where('market_id', '=', $market->id)->value('request_status');

        if ($request_status == null) {
            return Response()->json('you can not sent this order, you have to follow the market');
        } else if ($request_status == 'waiting') {
            return Response()->json('you can not sent this order, you have to waiting  until the market  accept your follow request');
        } else if ($request_status == 'rejected') {
            return Response()->json('you can not sent this order, the market  have to accept your follow request');
        } else if ($request_status == 'accepted') {

            $newOrder = $market->buyingOrder()->create(
                [
                    'user_id' => Auth::guard('api')->id(),
                    'market_id' => $market->id,
                    'order_date' => Carbon::now(),
                    'lat' => $request->lat,
                    'lang' => $request->lang,
                ]
            );

            foreach ($request->product as $item) {

                $order = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'details' => $item['details']
                ];

                $newOrder->product()->SyncWithoutDetaching([$order]);

            }
            //if customer want to deliver the order add it.
            if ($request->has('with_Delivery') && $request->with_Delivery != 'withOutDelivery') {
                $delivery = Delivery::query()->where('market_id', '=', $market->id)->get()->first();
               if($delivery != null) {
                   WithDelivery::query()->create([
                       'delivery_type' => $request->with_Delivery,
                       'order_id' => $newOrder->id,
                       'delivery_id' => $delivery->id,
                   ]);
               }
            }
            $userName = Auth::user()->name;
            $title = 'new order';
            $message = $userName . ' sent new Buying order';
            $ids [] = $market->id;
            Notifications::notify($ids, $title, $message, 'market');

            return Response()->json('Order created successfully', Response::HTTP_CREATED);

        } else
            return Response()->json('wrong');
    }

    public function cancelOrder(BuyingOrder $buyingOrder)
    {
        if ($buyingOrder->request_status != 'accepted' && $buyingOrder->user_id != Auth::guard('api')->id()) {
            return response()->json('you can not cancel this order');
        } else {
            DB::table('buying_order_products')->where('buying_order_id','=',$buyingOrder->id)->delete();
            $buyingOrder->delete();
            return response()->json('order deleted');

        }
    }

    public function acceptOrder(Request $request, BuyingOrder $buyingOrder)
    {

        $productId = $buyingOrder->product()->get()->toArray();

        foreach ($productId as $item) {
            $quantity = DB::table('markets_products')->where('market_id', '=', Auth::id())
                ->where('product_id', '=', $item['id'])
                ->pluck('quantity');
            if ($quantity[0] < $item['pivot']['quantity']) {
                return Response()->json('you cannot accept because the quantity dose not enough');
            }
        }

        if ($request->has('delivery_cost')) {

            $buyingOrder->withdelivery()->update(
                ['startTime' => Carbon::now()]
            );
            $buyingOrder->update(
                [
                    'delivery_cost' => $request->delivery_cost,
                    'request_status' => 'accepted',
                ]
            );

        } else {
            $buyingOrder->update(
                [
                    'request_status' => 'accepted',
                    'delivery_cost' => 0
                ]
            );
        }
        $userName = Auth::guard('market-api')->user()->name;
        $title = 'order accepted';
        $message = $userName . 'accept your Buying order';
        $ids [] = $buyingOrder->user_id;
        Notifications::notify($ids, $title, $message, 'market');

        return Response()->json('buying Order accepted', Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateBuyingOrderRequest $request
     * @param \App\Models\BuyingOrder $buyingOrder
     * @return \Illuminate\Http\Response
     */
    public function rejectOrder(BuyingOrder $buyingOrder): \Illuminate\Http\jsonResponse
    {
        $buyingOrder->update(
            ['request_status' => 'rejected']
        );
        $userName = Auth::guard('market-api')->user()->name;
        $title = 'order rejected';
        $message = $userName . 'reject your Buying order';
        $ids [] = $buyingOrder->user_id;
        Notifications::notify($ids, $title, $message, 'market');

        return Response()->json('Order rejected successfully', Response::HTTP_OK);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\BuyingOrder $buyingOrder
     * @return \Illuminate\Http\Response
     */
    //market
    public function showTheOrders(): \Illuminate\Http\jsonResponse
    {
        $buyingOrder = BuyingOrder::query()
            ->where('market_id', '=', Auth::guard('market-api')->id())
            ->with('product')
            ->get();

        return Response()->json($buyingOrder, Response::HTTP_OK);

    }

    public function showTheWaitingOrder()
    {
        $buyingOrder = BuyingOrder::query()->where('market_id', '=', Auth::guard('market-api')->id())
            ->where('request_status', '=', 'waiting')
            ->with('product')->get();
        return Response()->json($buyingOrder, Response::HTTP_OK);

    }

    public function showTheRejectedOrder()
    {
        $buyingOrder = BuyingOrder::query()->where('market_id', '=', Auth::guard('market-api')->id())
            ->where('request_status', '=', 'rejected')
            ->with('product')->get();
        return Response()->json($buyingOrder, Response::HTTP_OK);

    }

    public function showTheAcceptedOrder()
    {
        $buyingOrder = BuyingOrder::query()->where('market_id', '=', Auth::guard('market-api')->id())
            ->where('request_status', '=', 'accepted')
            ->with('product')->get();
        return Response()->json($buyingOrder, Response::HTTP_OK);

    }

    //customer
    public function showMyOrders(): \Illuminate\Http\jsonResponse
    {
        $buyingOrder = BuyingOrder::query()->where('user_id', '=', Auth::guard('api')->id())
            ->with('product')->get();
        return Response()->json($buyingOrder, Response::HTTP_OK);

    }

    public function showMyWaitingOrder()
    {
        $buyingOrder = BuyingOrder::query()->where('user_id', '=', Auth::guard('api')->id())
            ->where('request_status', '=', 'waiting')
            ->with('product')->get();
        return Response()->json($buyingOrder, Response::HTTP_OK);

    }

    public function showMyRejectedOrder()
    {
        $buyingOrder = BuyingOrder::query()->where('user_id', '=', Auth::guard('api')->id())
            ->where('request_status', '=', 'rejected')
            ->with('product')->get();
        return Response()->json($buyingOrder, Response::HTTP_OK);

    }

    public function showMyAcceptedOrder()
    {
        $buyingOrder = BuyingOrder::query()->where('user_id', '=', Auth::guard('api')->id())
            ->where('request_status', '=', 'accepted')
            ->with('product')->get();
        return Response()->json($buyingOrder, Response::HTTP_OK);

    }

    public function WaitToDeliverOrder()
    {
        $delivery = WithDelivery::query()->where('delivery_id', '=', Auth::guard('delivery-api')->id())
            ->join('buying_orders', 'order_id', '=', 'buying_orders.id')
            ->where('buying_orders.request_status', '=', 'accepted')
            ->where('buying_orders.is_received', '=', false)
            ->join('users', 'buying_orders.user_id', '=', 'users.id')
            ->join('buying_order_products', 'buying_orders.id', '=', 'buying_order_id')
            ->join('products', 'product_id', '=', 'products.id')
            ->select(
                'delivery_type', 'order_id',
                'users.name', 'users.phone', 'users.location',
                'buying_orders.total_price', 'buying_orders.is_received', 'buying_orders.request_status', 'buying_orders.delivery_cost',
                'buying_orders.lat', 'buying_orders.lang',
                'products.name as product_name', 'buying_order_products.quantity', 'buying_order_products.details'
            )
            ->get();

        return Response()->json($delivery, Response::HTTP_OK);

    }

    public function showDeliveredOrder()
    {
        $delivery = WithDelivery::query()->where('delivery_id', '=', Auth::guard('delivery-api')->id())
            ->join('buying_orders', 'order_id', '=', 'buying_orders.id')
            ->where('buying_orders.is_received', '=', true)
            ->join('users', 'buying_orders.user_id', '=', 'users.id')
            ->join('buying_order_products', 'buying_orders.id', '=', 'buying_order_id')
            ->join('products', 'product_id', '=', 'products.id')
            ->select(
                'delivery_type', 'order_id',
                'users.name', 'users.phone', 'users.location',
                'buying_orders.total_price', 'buying_orders.is_received', 'buying_orders.request_status', 'buying_orders.delivery_cost',
                'buying_orders.lat', 'buying_orders.lang',
                'products.name as product_name', 'buying_order_products.quantity', 'buying_order_products.details'
                ,'with_deliveries.endTime','with_deliveries.startTime',
            )
            ->get();
        return Response()->json($delivery, Response::HTTP_OK);

    }
    public function MarketshowDeliveredOrder()
    {
        $market_id=Auth::guard('market-api')->id();
        $delivery = WithDelivery::query()->where('market_id', '=', $market_id)
            ->join('buying_orders', 'order_id', '=', 'buying_orders.id')
            ->where('buying_orders.is_received', '=', true)
            ->join('users', 'buying_orders.user_id', '=', 'users.id')
            ->join('buying_order_products', 'buying_orders.id', '=', 'buying_order_id')
            ->join('products', 'product_id', '=', 'products.id')
            ->select(
                'delivery_type', 'order_id',
                'users.name', 'users.phone', 'users.location',
                'buying_orders.total_price', 'buying_orders.is_received', 'buying_orders.request_status', 'buying_orders.delivery_cost',
                'buying_orders.lat', 'buying_orders.lang',
                'products.name as product_name', 'buying_order_products.quantity', 'buying_order_products.details'
                ,'with_deliveries.endTime','with_deliveries.startTime',
            )
            ->get();
        return Response()->json($delivery, Response::HTTP_OK);

    }

    public function MarketReceiptConfirmation(BuyingOrder $buyingOrder)
    {

        if (strcmp('accepted', $buyingOrder['request_status'])) {
            return response()
                ->json('you can not send this order, you have to waiting  until the market  accept your order');

        }
        $price = 0;

        $buyingOrder->update(['is_received' => true]);

        $OrderedProducts = DB::table('buying_order_products')->where('buying_order_id', '=', $buyingOrder->id)
            ->get();

        foreach ($OrderedProducts as $orderedProduct) {
            DB::table('markets_products')
                ->where('market_id', '=', Auth::guard('market-api')->id())
                ->where('product_id', '=', $orderedProduct->product_id)
                ->decrement('quantity', $orderedProduct->quantity);


            $market_product = DB::table('markets_products')
                ->where('market_id', '=',  Auth::guard('market-api')->id())
                ->where('product_id', '=', $orderedProduct->product_id)
                ->get();
            $price = $price + $market_product[0]->price * $orderedProduct->quantity;

        }

        $buyingOrder->update(['total_price' => $price + $buyingOrder->delivery_cost]);

        WithDelivery::query()->where('order_id', '=', $buyingOrder->id)->update(
            ['endTime' => Carbon::now()]
        );
        $delivery = WithDelivery::query()->where('order_id', '=', $buyingOrder->id)->get();

        $result['buyingOrder'] = $buyingOrder;
        $result['products'] = $OrderedProducts;
        $result['delivery'] = $delivery;

        return response()->json($result, Response::HTTP_OK);


    }

    public function DeliveryReceiptConfirmation(BuyingOrder $buyingOrder)
    {
        $delivery=Auth::guard('delivery-api')->user();

        if (strcmp('accepted', $buyingOrder['request_status'])) {
            return response()
                ->json('you can not send this order, you have to waiting  until the market  accept your order');

        }
        $price = 0;

        $buyingOrder->update(['is_received' => true]);

        $OrderedProducts = DB::table('buying_order_products')
            ->where('buying_order_id', '=', $buyingOrder->id)
            ->get();

        foreach ($OrderedProducts as $orderedProduct) {
            DB::table('markets_products')
                ->where('market_id', '=', $delivery->market_id)
                ->where('product_id', '=', $orderedProduct->product_id)
                ->decrement('quantity', $orderedProduct->quantity);


            $market_product = DB::table('markets_products')
                ->where('market_id', '=',  $delivery->market_id)
                ->where('product_id', '=', $orderedProduct->product_id)
                ->get();
            $price = $price + $market_product[0]->price * $orderedProduct->quantity;

        }

        $buyingOrder->update(['total_price' => $price + $buyingOrder->delivery_cost]);

        WithDelivery::query()->where('order_id', '=', $buyingOrder->id)->update(
            ['endTime' => Carbon::now()]
        );
        $delivery = WithDelivery::query()->where('order_id', '=', $buyingOrder->id)->get();

        $result['buyingOrder'] = $buyingOrder;
        $result['products'] = $OrderedProducts;
        $result['delivery'] = $delivery;

        return response()->json($result, Response::HTTP_OK);


    }

}
