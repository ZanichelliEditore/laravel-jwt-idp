<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        DB::table('users')->insert([
            'email' => 'mario.rossi@example.com',
            'password' => Hash::make('secret'),
            'is_verified' => true,
            'name' => 'Mario',
            'surname' => 'Rossi'
        ]);

        DB::table('roles')->insert([
            'name' => 'USER'
        ]);

        DB::table('roles')->insert([
            'name' => 'ADMIN'
        ]);

        DB::table('user_roles')->insert([
            'user_id' => 1,
            'role_id' => 1
        ]);

        DB::table('user_roles')->insert([
            'user_id' => 1,
            'role_id' => 2
        ]);

    }

}
