<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;

class BlogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Blog::truncate();
        $faker = \Faker\Factory::create();
        for ($i=0; $i<50; $i++) {
        	Blog::create([
        		'title' => $faker->sentence,
        		'content' => $faker->paragraph,
                'created_by' => 1
        	]);
        }
    }
}
