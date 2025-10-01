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
        User::firstOrCreate(
            [
                'email' => 'test@example.com'
            ],
            [
                'name' => 'テストユーザー',
                'password' => Hash::make('password'),
            ]
        );

        User::firstOrCreate(
            [
                'email' => 'other@example.com'
            ],
            [
                'name' => '他のユーザー',
                'password' => Hash::make('password'),
            ]
        );
    }
}
