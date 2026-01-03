<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventOrganizerSeeder extends Seeder
{
   public function run()
   {
      $faker = \Faker\Factory::create();
      $db = \Config\Database::connect();

      for ($i = 0; $i < 10; $i++) {
         $data = [
            'org_event_id' => $faker->numberBetween(1, 10),
            'org_organizer_name' => $faker->name(),
            'org_organizer_email' => $faker->email(),
            'org_organizer_phone' => $faker->phoneNumber(),
            'org_organizer_company' => $faker->company(),
            'org_organizer_title' => $faker->jobTitle(),
            'org_organizer_role' => $faker->jobTitle(),
            'org_organizer_website' => $faker->url(),
            'org_organizer_fb_link' => $faker->url(),
            'org_organizer_insta_link' => $faker->url(),
            'org_organizer_twitter_link' => $faker->url(),
            'org_organizer_linkedin_link' => $faker->url(),
            'created_at' => date('Y-m-d H:i:s'),
         ];

         $db->table('event_organizers')->insert($data);
      }
   }
}