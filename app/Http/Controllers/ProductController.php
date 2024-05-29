<?php

namespace App\Http\Controllers;

use App\Http\Traits\Images;
use App\Imports\ExcelImport;
use App\Models\Market;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

//use http\Env\Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use League\OAuth2\Server\RequestEvent;
use Maatwebsite\Excel\Facades\Excel;
use phpseclib3\Math\BinaryField\Integer;
use Symfony\Component\HttpFoundation\Response;

//use Maatwebsite\Excel\Excel;

class ProductController extends Controller
{

    public function import()
    {
        $productIds = 0;
        //get market id to sync the product which added it
        $market = Auth::guard('market-api')->user();
        //saved products  in database(product table) from excel file
        $products = Excel::import(new ExcelImport, request()->file('file'))
            ->toArray(new ExcelImport, request()->file('file'));
        //check if the product exist in the product table to sync with its id

        $existnow = Product::query()->where('created_at', '=', Carbon::now())->pluck('id')->toArray();

        $productexist = ExcelImport::$existId;
        if ($existnow == null) {
            $productIds = $productexist;
        } elseif ($productexist == null) {
            $productIds = $existnow;

        } else {
            for ($i = 0; $i < count($existnow); $i++) {
                for ($j = 0; $j < count($productexist); $j++) {
                    if ($productexist[$j] == null) {
                        $productexist[$j] = $existnow[$i];
                        $i++;
                    }
                }
            }
            $productIds = $productexist;

        }
        //get product ids  that have been added

        $count = count($productIds);
        for ($i = 0; $i < $count; $i++) {
            foreach ($products as $item) {
                $productDetails = [
                    'price' => $item[$i][2],
                    'quantity' => $item[$i][3],
                    'details' => $item[$i][4],
                    'product_id' => $productIds[$i]
                ];

                //sync this product with the market which added it

                $market->product()->SyncWithoutDetaching([$productDetails]);
            }
        }
        return Response()->json($products);
    }

    public function showMyProducts(): \Illuminate\Http\JsonResponse
    {
        $market = Auth::guard('market-api')->user();
        $myProducts = $market->product()->get();
        return Response()->json($myProducts, Response::HTTP_OK);
    }

//    public function addProduct(Request $request)
//    {
//        $market = Auth::guard('market-api')->user();
//
//        $validator = Validator::make($request->all(), [
//            'name' => ['required'],
//            'description' => ['required','string'],
//            'price'=>['required'],
//            'quantity'=>['required'],
//            'photo' => ['present','array'],
//            'photo.*' => ['file','mimes:png,jpeg,svg,bmp,jpg'],
//        ]);
//
//        if ($validator->fails()) {
//            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
//        }
//
//
//        $existId = Product::query()->where('name', '=', $request->name)
//            ->where('description', '=', $request->description)->pluck('id')->first();
//
//
//        if ($existId == null) {
//
//            $image_url = Images::SaveProductImage($request);
//
//            $product = Product::query()->create(
//                [
//                    'name' => $request->name,
//                    'description' => $request->description
//                ]
//            );
//            $product->save();
//
//            foreach ($image_url as $item)
//                $product->photo()->create(
//                    [
//                        'url' => $item,
//                    ]);
//
//            $productDetails =
//                [
//                    'price' => $request->price,
//                    'quantity' => $request->quantity,
//                    'details' => $request->description,
//                    'product_id' => $product->id,
//                ];
//            $market->product()->SyncWithoutDetaching([$productDetails]);
//
//        } else {
//           // $product = Product::query()->where('id', '=', $existId)->get();
//
//            $productDetails =
//                [
//                    'price' => $request->price,
//                    'quantity' => $request->quantity,
//                    'details' => $request->description,
//                    'product_id' => $existId
//                ];
//
//            $myproduct=$market->product()->where('product_id','=',$existId); *******************************
//            if($myproduct==null)
//            {
//                $market->product()->SyncWithoutDetaching([$productDetails]);
//            }
//            else
//            {
//                $myproduct->update( ****************************************************************
//                    ['price' => $request->price,
//                        'quantity' => $request->quantity,
//                        'details' => $request->description
//                    ]);
//            }
//
//        }
//
//
//
//        return Response()->json('product created successfully', Response::HTTP_OK);
//    }

    public function addProduct(Request $request)
    {
        $market = Auth::guard('market-api')->user();

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'description' => ['required','string'],
            'price'=>['required'],
            'quantity'=>['required'],
            'photo' => ['present','array'],
            'photo.*' => ['file','mimes:png,jpeg,svg,bmp,jpg'],
        ]);

        if ($validator->fails()) {
            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

//سؤال هون ليش عم نقارن الوصف كمان
        $existId = Product::query()->where('name', '=', $request->name)
            ->where('description', '=', $request->description)->pluck('id')->first();

        if ($existId == null) {

            $image_url = Images::SaveProductImage($request);

            $product = Product::query()->create(
                [
                    'name' => $request->name,
                    'description' => $request->description
                ]
            );
            $product->save();

            foreach ($image_url as $item)
                $product->photo()->create(
                    [
                        'url' => $item,
                    ]);

            $productDetails =
                [
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'details' => $request->description,
                    'product_id' => $product->id,
                ];
            $market->product()->SyncWithoutDetaching([$productDetails]);

        } else {
            $product = Product::query()->where('id', '=', $existId)->get();

            $productDetails =
                [
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'details' => $request->description,
                    'product_id' => $existId
                ];

            $myproduct=$market->product()->where('product_id','=',$existId)->first();
           // dd($myproduct);
            if($myproduct==null)
            {             //   dd('if');

                $market->product()->SyncWithoutDetaching([$productDetails]);
            }
            else
            {
               // dd('else');
                $myproduct->pivot->update(
                    [   'price' => $request->price,
                        'quantity' => $request->quantity,
                        'details' => $request->description]);
            }

        }

        return Response()->json('product created successfully', Response::HTTP_OK);
    }
    public function editMyProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id'=>['required'],
            'price' => ['required'],
            'quantity' => ['required'],
            'details'=>['required'],
        ]);

        if ($validator->fails()) {
            return Response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $market = Auth::guard('market-api')->user();
        $market->product()->where('product_id','=',$request->product_id)->update(
            [
                'price' => $request->price,
                'quantity' => $request->quantity,
                'details' => $request->details
            ]
        );
        $Product=$market->product()->where('product_id','=',$request->product_id)->get();
        return Response()->json($Product, Response::HTTP_OK);
    }

    public function destroy(Product $product): \Illuminate\Http\JsonResponse
    {
        $product->delete();

        return Response()->json('product deleted successfully', Response::HTTP_OK);
    }


}
