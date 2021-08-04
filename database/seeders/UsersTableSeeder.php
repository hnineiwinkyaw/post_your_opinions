<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $faker = \Faker\Factory::create();
        $password = Hash::make('password');

        User::create([
        	'name' => 'Admin',
        	'email' => 'admin@blogingsystem.com',
        	'password' => $password,
        ]);

        for ($i=0; $i<10; $i++) {
        	User::create([
        		'name' => $faker->name,
        		'email' => $faker->email,
        		'password' => $password,
        	]);
        }
    }
}
