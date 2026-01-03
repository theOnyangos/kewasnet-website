<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSubCategorySeeder extends Seeder
{
   public function run()
   {
      $data = [
            [
                'id' => 'ab2a762b-c9c3-4966-ae86-fcd7a26801af',
            'csc_category_id' => 1,
            'csc_name' => 'coagulation',
            'csc_slug' => 'coagulation',
            'csc_description' => 'Coagulation is often the first step in water treatment. During coagulation, chemicals with a positive charge are added to the water. The positive charge neutralizes the negative charge of dirt and other dissolved particles in the water. When this occurs, the particles bind with the chemicals to form slightly larger particles. Common chemicals used in this step include specific types of salts, aluminum, or iron.',
         ],
            [
                'id' => '71b0dd38-0df9-4b5c-98f2-f4eb689771be',
            'csc_category_id' => 1,
            'csc_name' => 'flocculation',
            'csc_slug' => 'flocculation',
            'csc_description' => 'Flocculation is the process of agglomerating these smaller particles into larger particles, called floc. Flocculation is accomplished in the water treatment plant by adding chemicals to the water to create conditions that encourage the particles to form larger particles. The flocculation process is often aided by the use of mechanical mixing.',
         ],
            [
                'id' => '7fce9971-a25b-4f44-bde4-a37c8a3985d9',
            'csc_category_id' => 1,
            'csc_name' => 'sedimentation',
            'csc_slug' => 'sedimentation',
            'csc_description' => 'Sedimentation is the process of removing suspended coarser particles in water by gravitational settling. The settling velocity of a particle is governed by Stoke’s law. The settling velocity is also affected by the particle shape, size, and density. The settling velocity is inversely proportional to the viscosity of water. The settling velocity is directly proportional to the difference between the specific gravity of the particle and water. The settling velocity is directly proportional to the square of the particle diameter.',
         ],
            [
                'id' => '24298c79-b4f9-4161-8da1-e024b16ca836',
            'csc_category_id' => 2,//water distribution systems
            'csc_name' => 'ring system',
            'csc_slug' => 'ring-system',
            'csc_description' => 'Ring system is a system in which the water is supplied from two directions. The water is supplied from two directions to the junction point. The junction point is connected to the main supply line. The ring system is suitable for the areas where the water supply is required at a constant pressure.',
         ],
            [
                'id' => '098cc756-d415-4fb6-a237-ecf550e0824c',
            'csc_category_id' => 2,//water distribution systems
            'csc_name' => 'grid iron system',
            'csc_slug' => 'grid-iron-system',
            'csc_description' => 'Grid iron system is a system in which the water is supplied from two directions. The water is supplied from two directions to the junction point. The junction point is connected to the main supply line. The grid iron system is suitable for the areas where the water supply is required at a constant pressure.',
         ]
      ];

      // Using Query Builder
      $courseSubCategoryModel = model('App\Models\CourseSubCategory', false);

      $courseSubCategoryModel->insertBatch($data);
   }
}
