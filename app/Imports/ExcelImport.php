<?php

namespace App\Imports;

use App\Models\Market;
use App\Models\Product;
use http\Env\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;

class ExcelImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return Product
     */
    static $existId;

    public function model(array $row)
    {

        ExcelImport::$existId[] = Product::query()->where('name', '=', $row[0])
            ->where('description', '=', $row[1])->pluck('id')->first();

        $exist = Product::query()->where('name', '=', $row[0])
            ->where('description', '=', $row[1])->pluck('id')->first();
        if ($exist == null) {
            $product = new Product([
                'name' => $row[0],
                'description' => $row[1]
            ]);


        } else {
            $product = null;
        }
        $exist = null;
        return $product;
    }







}


