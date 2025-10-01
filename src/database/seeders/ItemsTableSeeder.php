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

        $user      = DB::table('users')->where('email', 'test@example.com')->first();
        $otherUser = DB::table('users')->where('email', 'other@example.com')->first();

        DB::table('items')->insert([
            [
                'user_id' => $user->id,
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image' => 'images/腕時計.jpg',
                'condition' => 1,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $otherUser->id,
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'item_image' => 'images/HDD.jpg',
                'condition' => 2,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'item_image' => 'images/玉ねぎ3束.jpg',
                'condition' => 3,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $otherUser->id,
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'item_image' => 'images/革靴.jpg',
                'condition' => 4,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'item_image' => 'images/ノートPC.jpg',
                'condition' => 1,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $otherUser->id,
                'name' => 'マイク',
                'price' => 8000,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'item_image' => 'images/マイク.jpg',
                'condition' => 2,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'item_image' => 'images/ショルダーバッグ.jpg',
                'condition' => 3,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $otherUser->id,
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'item_image' => 'images/タンブラー.jpg',
                'condition' => 4,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'item_image' => 'images/コーヒーミル.jpg',
                'condition' => 1,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $otherUser->id,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'item_image' => 'images/メイクセット.jpg',
                'condition' => 2,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
