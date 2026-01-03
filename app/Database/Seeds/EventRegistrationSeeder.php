<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventRegistrationSeeder extends Seeder
{
   public function run()
   {
      $faker = \Faker\Factory::create();
      $db = \Config\Database::connect();

      for ($i = 0; $i < 50; $i++) {
         $data = [
            'event_id' =>  $faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
            'attendee_name' => $faker->name,
            'phone' => $faker->phoneNumber,
            'email' => $faker->email,
            'is_paid' => $faker->boolean,
            'created_at' => date('Y-m-d H:i:s'),
         ];

         $db->table('event_registrations')->insert($data);
      }
   }
}
