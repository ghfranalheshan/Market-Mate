<?php

namespace Database\Seeders;

use App\Models\M_category;
use App\Models\Market;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\M_CategoryFactory;

class MCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        M_category::query()->create(['name'=>'Beauty']);
        M_category::query()->create(['name'=>'clothes']);

    }
}
