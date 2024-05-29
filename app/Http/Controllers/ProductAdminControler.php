<?php

namespace App\Http\Controllers;

use App\Http\Traits\Images;
use App\Models\BuyingOrder;
use App\Models\Market;
use App\Models\Photo;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

class ProductAdminControler extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $products = Product::query()->with('photo')->get();

        return view('Products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('Products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique('products')],
            'description' => ['required', 'string'],
            'photo' => ['required', 'image:jpeg,png,jpg,gif,svg', 'max:2048']
        ]);
        if ($validator->fails()) {
            return redirect()->route('MCategory.index')
                ->with('Failed', $validator->errors());
        }


        $image = $request->file('photo');
        $product_Image = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('Products'), $product_Image);
        $product_Image = 'Products/' . $product_Image;

        $product=Product::query()->create([
            'name'=>$request->name,
            'description'=>$request->description,
        ]);

        $product->photo()->create(
            [
                'url' => $product_Image,
            ]);

//        Photo::query()->create([
//            'url'=>$image_url,
//            'related_id'=>$product->id,
//            'related_type'=>'App\Models\Product'
//        ]);

        return redirect()->route('Product.index')
            ->with('Success', 'Product Created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $product = Product::query()->where('id', '=', $id)->first();
        $photo = $product->photo->first();
        $photo = $photo->url;

        return view('Products.show', compact('product', 'photo'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $product = Product::query()->where('id', '=', $id)->first();
        return view('Products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', Rule::unique('products')->ignore($id)],
            'description' => ['required', 'string'],
            'photo' => ['required', 'image:jpeg,png,jpg,gif,svg', 'max:2048']
        ]);
        if ($validator->fails()) {
            return redirect()->route('Product.index')
                ->with('Failed', $validator->errors());
        }
        $image_url = Images::SaveProductImage($request);

        $product = Product::query()->where('id', '=', $id)->first();

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        Photo::query()->where('related_id', '=', $product->id)->delete();

        //don't forget to find a way to delete photo from server storage too. not just from DB

        Photo::query()->create([
            'url' => $image_url,
            'related_id' => $product->id,
            'related_type' => 'App\Models\Product'
        ]);


        return redirect()->route('Product.index')
            ->with('Success', 'Product Updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        Product::query()->where('id', '=', $id)->delete();

        return redirect()->route('Product.index')
            ->with('Success', 'Product deleted successfully');

    }

    public function total(){
        $total['customer']=User::query()->count() -1 ;
        $total['market']=Market::query()->count();
        //$total['product']=5;

//        $total['product_June']=Product::query()->whereBetween('created_at',['2023-06-00','2023-06-30'])->count();
//        $total['product_July']=Product::query()->whereBetween('created_at',['2023-07-00','2023-07-30'])->count();;
//        $total['product_august']=Product::query()->whereBetween('created_at',['2023-08-00','2023-08-30'])->count();;
        $total['product_June']=10;
            $total['product_July']=15;
            $total['product_august']=8;

        $productdata = [
            $total['product_June'],
            $total['product_July'],
            $total['product_august'],
        ];

//        $totalorder['order_June']=BuyingOrder::query()->whereBetween('created_at',['2023-06-00','2023-06-30'])->count();
//        $totalorder['order_July']=BuyingOrder::query()->whereBetween('created_at',['2023-07-00','2023-07-30'])->count();;
//        $totalorder['order_august']=BuyingOrder::query()->whereBetween('created_at',['2023-08-00','2023-08-30'])->count();

        $totalorder['order_June']=30;
        $totalorder['order_July']=20;
        $totalorder['order_august']=15;

        $orderdata = [
            $totalorder['order_June'],
            $totalorder['order_July'],
            $totalorder['order_august'],
        ];
        return view('myHome',compact('total','orderdata','productdata'));

    }
}
