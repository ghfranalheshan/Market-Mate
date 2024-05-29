<?php

namespace App\Http\Traits;

use App\Http\Requests\StoreProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Array_;

trait Images
{

    public static function SaveUserImage(Request $request): string
    {
        $image = $request->file('photo');
        $UsersProfile_Image = null;

        if ($request->hasFile('photo')) {
            $UsersProfile_Image = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('UsersProfile'), $UsersProfile_Image);
            $UsersProfile_Image = 'UsersProfile/' . $UsersProfile_Image;
        }

        return $UsersProfile_Image;

    }

    public static function SaveProductImage(Request $request)
    {
        $Images = [] ;
        $photos=$request->file('photo');
        foreach ( $photos as $image) {

            $product_Image = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Products'), $product_Image);
            $product_Image = 'Products/' . $product_Image;
            $Images = Arr::prepend($Images,$product_Image);
        }

        return $Images;

    }
    public static function SavePostImages(Request $request)
    {
        $Images = [] ;
        $photos=$request->file('photo');
        foreach ( $photos as $image) {

            $product_Image = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('Posts'), $product_Image);
            $photo= 'Posts/' . $product_Image;
            $Images = Arr::prepend($Images,$photo);

        }

        return $Images;
    }

    public static function SaveUserSocialImage($image): string
    {

        $UsersProfile_Image = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('UsersProfile'), $UsersProfile_Image);
        $UsersProfile_Image = 'UsersProfile/' . $UsersProfile_Image;

        return $UsersProfile_Image;

    }


    public static function deletePhoto($photos){

        foreach ($photos as $photo){
            $file=$photo->url;
            Storage::delete($file);
        }
    }

}
