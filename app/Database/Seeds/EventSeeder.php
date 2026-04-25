<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class EventSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        $db = \Config\Database::connect();

        for ($i = 0; $i < 10; $i++) {
            $startDateTime = $faker->dateTimeBetween('-1 month', '+1 month');
            $endWindow = (clone $startDateTime)->modify('+2 days');
            $endDateTime = $faker->dateTimeBetween($startDateTime, $endWindow);

            $data = [
                'id' => Uuid::uuid4()->toString(),
                'title' => $faker->sentence(),
                'slug' => $faker->unique()->slug(),
                'description' => $faker->paragraphs(3, true),
                'event_type' => $faker->randomElement(['paid', 'free']),
                'start_date' => $startDateTime->format('Y-m-d'),
                'end_date' => $faker->boolean(70) ? $endDateTime->format('Y-m-d') : null,
                'start_time' => $faker->boolean(80) ? $startDateTime->format('H:i:s') : null,
                'end_time' => $faker->boolean(70) ? $endDateTime->format('H:i:s') : null,
                'venue' => $faker->company(),
                'address' => $faker->address(),
                'city' => $faker->city(),
                'country' => $faker->country(),
                'image_url' => $faker->imageUrl(),
                'banner_url' => $faker->imageUrl(1280, 720),
                'total_capacity' => $faker->boolean(80) ? $faker->numberBetween(50, 1000) : null,
                'status' => $faker->randomElement(['draft', 'published']),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $db->table('events')->insert($data);
        }
    }
}
