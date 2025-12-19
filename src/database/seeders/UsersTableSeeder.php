<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ダミーデータ CO01～CO05 を出品したユーザー
        User::firstOrCreate(
            [
                'email' => 'test1@example.com'
            ],
            [
                'name' => 'テストユーザー1',
                'password' => Hash::make('password'),
            ]
        );

        // ダミーデータ CO06～CO10 を出品したユーザー
        User::firstOrCreate(
            [
                'email' => 'test2@example.com'
            ],
            [
                'name' => 'テストユーザー2',
                'password' => Hash::make('password'),
            ]
        );

        // 何も出品してないユーザー
        User::firstOrCreate(
            [
                'email' => 'test@example.com'
            ],
            [
                'name' => 'テストユーザー',
                'password' => Hash::make('password'),
            ]
        );
    }
}
