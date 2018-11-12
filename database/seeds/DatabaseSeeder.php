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
            'surname' => 'Rossi',
            'is_employee' => true
        ]);

        DB::table('roles')->insert([
            'name' => 'FUNZIONARIO'
        ]);

        DB::table('roles')->insert([
            'name' => 'DIRETTORE'
        ]);

        DB::table('departments')->insert([
            'name' => 'COMMERCIALE'
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
