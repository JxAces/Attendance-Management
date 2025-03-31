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
            'college' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 2,
            'name' => 'user',
            'email' => 'user@softui.com',
            'password' => Hash::make('user'),
            'college' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 3,
            'name' => 'CASS',
            'email' => 'cass@iit.com',
            'password' => Hash::make('user'),
            'college' => 'CASS',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 4,
            'name' => 'CCS',
            'email' => 'ccs@iit.com',
            'password' => Hash::make('user'),
            'college' => 'CCS',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 5,
            'name' => 'CED',
            'email' => 'ced@iit.com',
            'password' => Hash::make('user'),
            'college' => 'CED',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 6,
            'name' => 'CHS',
            'email' => 'chs@iit.com',
            'password' => Hash::make('user'),
            'college' => 'CHS',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('users')->insert([
            'id' => 7,
            'name' => 'COE',
            'email' => 'coe@iit.com',
            'password' => Hash::make('user'),
            'college' => 'COE',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
