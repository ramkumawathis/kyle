<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'first_name'     => 'Super',
                'last_name'      => 'Admin',
                'name'           => 'Super Admin',
                'email'          => 'amin@admin.com',
                'password'       => bcrypt('password'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],        
            [
                'id'             => 2,
                'first_name'     => 'Seller',
                'last_name'      => 'Role',
                'name'           => 'Seller Role',
                'email'          => 'seller@gmail.com',
                'password'       => bcrypt('seller@1234'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];
        User::insert($users);       
    }
}
