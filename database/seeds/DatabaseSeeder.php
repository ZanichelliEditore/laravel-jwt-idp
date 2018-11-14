<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        DB::table('users')->insert([
            'username' => 'mario.rossi',
            'email' => 'mario.rossi@example.com',
            'is_verified' => true,
            'name' => 'Mario',
            'surname' => 'Rossi'
        ]);

        DB::table('roles')->insert([
            'name' => 'DIPENDENTE'
        ]);

        DB::table('roles')->insert([
            'name' => 'AMMINISTRATORE'
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
