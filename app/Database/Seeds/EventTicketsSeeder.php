<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Helpers\TicketsGenerator;

class EventTicketsSeeder extends Seeder
{
   public function run()
   {
      $faker = \Faker\Factory::create();
      $db = \Config\Database::connect();

      for ($i = 0; $i < 50; $i++) {
         $eventId = $faker->randomElement([1,2,3,4,5,6,7,8,9,10]);
         $data = [
            'event_id' => $eventId,
            'event_reg_id' => $faker->randomElement([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]),
            'ticket_type' => $faker->randomElement(['regular', 'standard', 'vip', 'vvip', 'staff', 'sponsor']),
            'ticket_code' => 'TKT-' . strtoupper($faker->bothify('???-####')),
            'created_at' => date('Y-m-d H:i:s'),
         ];

         $db->table('event_tickets')->insert($data);
      }
   }
}