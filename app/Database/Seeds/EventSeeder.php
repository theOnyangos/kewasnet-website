<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventSeeder extends Seeder
{
   public function run()
   {
      $faker = \Faker\Factory::create();
      $db = \Config\Database::connect();

      for ($i = 0; $i < 10; $i++) {
         $start_date = $faker->dateTimeBetween('2024-01-01','+1 week')->format('Y-m-d H:i:s');
         $end_date = $faker->dateTimeBetween($start_date, '+1 week')->format('Y-m-d H:i:s');

         $data = [
            'admin_id' => 1, // Will be updated when we have proper admin UUIDs
            'title' => $faker->sentence(),
            'slug' => $faker->slug(),
            'summary' => $faker->paragraph(),
            'description' => $faker->text(),
            'is_paid' => $faker->boolean(),
            'price' => $faker->randomFloat(2, 0, 10000),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'location' => $faker->city(),
            'event_cover_image' => $faker->imageUrl(),
            'longitude' => $faker->longitude(),
            'latitude' => $faker->latitude(),
            'created_at' => date('Y-m-d H:i:s'),
         ];

         $db->table('events')->insert($data);
      }
   }
}
