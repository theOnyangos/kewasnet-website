<?php

namespace App\Database\Seeds;

use App\Helpers\SlugHelper;
use CodeIgniter\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
   public function run()
   {
      $data = [
            [
                'id' => '99d1c776-9ba6-4c45-8e6e-3f3a87d64f43',
            'cc_name' => 'Water Treatment',
            'cc_slug' => SlugHelper::createSlug('Water Treatment'),
            'cc_description' => 'Water Treatment Course Category',
            'cc_status' =>1,
         ],
            [
                'id' => 'ce382183-e769-483e-a176-d2d3c4d6cbc9',
            'cc_name'=>'Sewage Management',
            'cc_slug'=>SlugHelper::createSlug('Sewage Management'),
            'cc_description'=>'Sewage Management Course Category',
            'cc_status'=>1,
         ],
            [
                'id' => 'b9250389-4db5-486f-aaf8-754230abff48',
            'cc_name'=>'Water Distribution',
            'cc_slug'=>SlugHelper::createSlug('Water Distribution'),
            'cc_description'=>'Water Distribution Course Category',
            'cc_status'=>1,
         ],
            [
                'id' => 'da3bcdec-6253-4c1a-8dba-74ec872d377b',
            'cc_name'=>'Water Quality',
            'cc_slug'=>'water-quality',
            'cc_description'=>'Water Quality Course Category',
            'cc_status'=>1,
         ],
            [
                'id' => '513569c8-a126-4f36-a7b7-da962fc3a0dc',
            'cc_name'=>'Water Conservation',
            'cc_slug'=>'water-conservation',
            'cc_description'=>'Water Conservation Course Category',
            'cc_status'=>1,
         ],
            [
                'id' => '7a113f67-b031-4909-9e89-db3e413035a0',
            'cc_name'=>'Water Management',
            'cc_slug'=>'water-management',
            'cc_description'=>'Water Management Course Category',
            'cc_status'=>0,
         ],
      ];

      // Using Query Builder
      $db = \Config\Database::connect();
      $db->table('course_categories')->insertBatch($data);
   }
}
