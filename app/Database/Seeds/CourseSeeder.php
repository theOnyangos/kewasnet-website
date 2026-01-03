<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
   public function run()
   {
      $db = \Config\Database::connect();
      
      $data = [
         //courses in water treatement

            [
                'id' => '37d19b5c-6eb0-4431-810c-2cd499eb915e',
            'category_id' => 1,
            'sub_category_id' => 1,
            'title' => 'Introduction to Coagulation',
            'slug' => 'introduction-to-coagulation',
            'summary' => 'Introduction to Coagulation',
            'description' => 'Coagulation is often the first step in water treatment. During coagulation, chemicals with a positive charge are added to the water. The positive charge neutralizes the negative charge of dirt and other dissolved particles in the water. When this occurs, the particles bind with the chemicals to form slightly larger particles. Common chemicals used in this step include specific types of salts, aluminum, or iron.',
            'status' => 1,
            'certificate' => 1,
            'level' => 'Beginner',
            'price' => 0,
            'discount_price' => 0,
            'duration' => '2',
            'resources' => '[]',
            'image_url' => 'https://www.cdc.gov/healthywater/drinking/images/water-source-diagram.png?_=81309',
            'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
            'star_rating' => "Newest",
            'goals' => '["Understand the importance of coagulation in water treatment","Understand the importance of coagulation in water treatment"]',


         ],
            [
                'id' => '8541adea-0c68-4ac8-9ee5-f3a8c5362726',
            'category_id' => 1,
            'sub_category_id' => 1,
            'title' => 'Introduction to Flocculation',
            'c_slug' => 'introduction-to-flocculation',
            'c_title' => 'Introduction to Flocculation',
            'description' => 'Flocculation is the process of bringing together the destabilized, or “coagulated,” particles to form a larger agglomeration, or “floc.” Flocculation is gentle stirring or agitation to encourage the particles thus formed to agglomerate into masses large enough to settle or be filtered from solution.',
            'status' => 1,
            'certificate' => 1,
            'level' => 'Beginner',
            'price' => 0,
            'discount_price' => 0,
            'duration' => '2',
            'resources' => '[]',
            'image_url' => 'https://www.cdc.gov/healthywater/drinking/images/water-source-diagram.png?_=81309',
            'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
            'star_rating' => "Newest",
            'goals' => '["Understand the importance of flocculation in water treatment","Understand the importance of flocculation in water treatment"]',
         ],
            [
                'id' => '87cfd413-d3d3-4db3-88b3-e8e3594e7e07',
            'category_id' => 1,
            'sub_category_id' => 1,
            'title' => 'Introduction to Sedimentation',
            'c_slug' => 'introduction-to-sedimentation',
            'c_title' => 'Introduction to Sedimentation',
            'description' => 'Sedimentation is the process of removing suspended coarser particles in water by settling down them to the bottom of tank. For a particle to settle down, the flow velocity must be reduced. This process is carried out in a structure called sedimentation tank or settling tank.',
            'status' => 1,
            'certificate' => 1,
            'level' => 'Beginner',
            'price' => 0,
            'discount_price' => 0,
            'duration' => '2',
            'resources' => '[]',
            'image_url' => 'https://www.cdc.gov/healthywater/drinking/images/water-source-diagram.png?_=81309',
            'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
            'star_rating' => "Newest",
            'goals' => '["Understand the importance of sedimentation in water treatment","Understand the importance of sedimentation in water treatment"]',
         ],
            [
                'id' => '0c995911-7c6a-4140-8ce8-e1c4d163428e',
            'category_id' => 1,
            'sub_category_id' => 1,
            'title' => 'Introduction to Filtration',
            'c_slug' => 'introduction-to-filtration',
            'c_title' => 'Introduction to Filtration',
            'description' => 'Filtration is a process that removes particles from suspension in water. Removal takes place by a number of mechanisms that include straining, flocculation, sedimentation and surface capture. Filters can be categorised by the main method of capture, i.e. exclusion of particles at the surface of the filter media i.e. straining, or deposition within the media i.e. in-depth filtration.',
            'status' => 1,
            'certificate' => 1,
            'level' => 'Beginner',
            'price' => 0,
            'discount_price' => 0,
            'duration' => '2',
            'resources' => '[]',
            'image_url' => 'https://www.cdc.gov/healthywater/drinking/images/water-source-diagram.png?_=81309',
            'preview_video_url' => 'https://www.youtube.com/watch?v=u4k2XY-fJJY',
            'star_rating' => "Newest",
            'goals' => '["Understand the importance of filtration in water treatment","Understand the importance of filtration in water treatment"]',
         ],
      ];

      $db->table('courses')->insertBatch($data);
   }
}
