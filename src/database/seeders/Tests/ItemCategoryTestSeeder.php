<?php

namespace Database\Seeders\Tests;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemCategoryTestSeeder extends Seeder
{
    public function run()
    {
        $watch = Item::where('name', '腕時計')->first();
        $hdd   = Item::where('name', 'HDD')->first();

        $fashion = Category::where('name', 'ファッション')->first();
        $mens    = Category::where('name', 'メンズ')->first();
        $home    = Category::where('name', '家電')->first();

        // attachでリレーションを登録
        $watch->categories()->attach([$fashion->id, $mens->id]);
        $hdd->categories()->attach([$home->id]);
    }
}
