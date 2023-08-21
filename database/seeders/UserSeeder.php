<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'admin',
            'email' => 'admin@softui.com',
            'password' => Hash::make('secret'),
            'admin' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 2,
            'name' => 'user',
            'email' => 'user@softui.com',
            'password' => Hash::make('user'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 3,
            'name' => 'it',
            'email' => 'bsit@ccs',
            'password' => Hash::make('user'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 4,
            'name' => 'is',
            'email' => 'bsis@ccs',
            'password' => Hash::make('user'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 5,
            'name' => 'ca',
            'email' => 'bsca@ccs',
            'password' => Hash::make('user'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 6,
            'name' => 'cs',
            'email' => 'bscs@ccs',
            'password' => Hash::make('user'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
