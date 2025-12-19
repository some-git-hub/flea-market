<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testUser1 = DB::table('users')->where('email', 'test1@example.com')->first();
        $testUser2 = DB::table('users')->where('email', 'test2@example.com')->first();

        DB::table('items')->insert([
            [
                'user_id' => $testUser1->id,
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image' => 'items/Clock.jpg',
                'condition' => 1,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser1->id,
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'item_image' => 'items/HDD.jpg',
                'condition' => 2,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser1->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image' => 'items/Onion-3.jpg',
                'condition' => 3,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser1->id,
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'item_image' => 'items/LeatherShoes.jpg',
                'condition' => 4,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser1->id,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'item_image' => 'items/Laptop.jpg',
                'condition' => 1,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser2->id,
                'name' => 'マイク',
                'price' => 8000,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'item_image' => 'items/Microphone.jpg',
                'condition' => 2,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser2->id,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'item_image' => 'items/ShoulderBag.jpg',
                'condition' => 3,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser2->id,
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'item_image' => 'items/Tumbler.jpg',
                'condition' => 4,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser2->id,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'item_image' => 'items/CoffeeGrinder.jpg',
                'condition' => 1,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $testUser2->id,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'item_image' => 'items/MakeupSet.jpg',
                'condition' => 2,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
