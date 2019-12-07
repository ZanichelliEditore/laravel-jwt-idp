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

        DB::table('clients')->insert([
            'oauth_client_id' => 1,
            'roles' => '["manager"]',
        ]);

        DB::table('clients')->insert([
            'oauth_client_id' => 2,
            'roles' => '["manager", "admin"]',
        ]);

        DB::table('clients')->insert([
            'oauth_client_id' => 3,
            'roles' => '[]',
        ]);

        // STEP: seeding passport oauth_clients
        DB::table('oauth_clients')->insert([
            'user_id' => 1,
            'name' => 'manager',
            'secret' => 'HkZ5sCBaAKRH0B5CIlBGjNIQazfYDxi4EDth3ANa',
            'redirect' => 'http://localhost:8081/auth/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0
        ]);

        DB::table('oauth_clients')->insert([
            'user_id' => 2,
            'name' => 'admin',
            'secret' => '6ZWpCgKPYc93TbgKHKnZMiULFStw88lIvquDQETQ',
            'redirect' => 'http://localhost:8081/auth/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0
        ]);

        DB::table('oauth_clients')->insert([
            'user_id' => 3,
            'name' => 'client',
            'secret' => 'mydhRDjLRMNuubmmHfs8u2DURLEc91qoc6fS58kT',
            'redirect' => 'http://localhost:8081/auth/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0
        ]);
    }

}
