<?php

namespace App\Database\Seeds;

use App\Helpers\SlugHelper;
use CodeIgniter\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id' => 1,
                'category_id' => 1,
                'slug' => SlugHelper::createSlug('Writing Code like a Senior Developer in CI4'),
                'title' => 'Writing Code like a Senior Developer in C14',
                'summary' => 'In the evolving landscape of web development, CI4 has emerged as one of the prominent PHP frameworks for building elegant applications.',

                'content' => 'In the evolving landscape of web development, CI4 has emerged as one of the prominent PHP frameworks for building elegant applications. If you’re looking to elevate your coding to the senior developer level, it’s crucial not only to understand CI4’s features but also to know how to use them effectively. This article delves into best practices, and common pitfalls, and provides both wrong and right coding examples to guide you in writing clean, efficient, and scalable code in CI4.',

            ],

            [
                'user_id' => 1,
                'category_id' => 2,
                'slug' => SlugHelper::createSlug('How CHVs are impacting community lives'),
                'title' => 'How CHVs are impacting community lives',
                'summary' => 'John Aholo was one of the trained CHV during the WASH First sensitization program targeting CHVs in Uthiru Ward of Kabete Sub county. He narrates on how he decided to convince some of the colleagues to form a group, Uthiru Rescue Team. The group was to focus on supporting members of the community to get more information about Covid 19 and above all reach out to community functions like harambees, burials and weddings to ensure MOH guidelines are observed. .',

                'content' => 'At first, I used to feel like I made a wrong choice too forming the group since it was a volunteer movement which also would cost us resources from our own pockets. But in the Month of August the area MCA partnered with is and he was able to provide us with reflector jackets, masks and enough sanitizers to give out. This created a huge influence since as we moved in the communities, now we were able to provide the masks and sanitizers especially to the vulnerable. Later the county government also supported us with the test kits for temperatures check ups to help us refer suspected cases. More people are now will to come for temperature check ups even the children and this really shows the willingness of people to ensure they are safe. As a result, within a short span we have been identified as Covid 19 sensitization champions in our community.',

            ],
            [
                'user_id' => 1,
                'category_id' => 2,
                'slug' => SlugHelper::createSlug('Community-Media Engagement on Water, Sanitation, and Hygiene by KEWASNET in Kilifi County'),
                'title' => 'Community-Media Engagement on Water, Sanitation, and Hygiene by KEWASNET in Kilifi County',

                'summary' => 'Kenya has been dealing with the issue of water scarcity for decades. With a growing population, rising living costs, and a steadily rising poverty index, water scarcity means more suffering, particularly for women and girls, slower development, and increased health risks. Kilifi county is among the areas facing serious water shortages.',

                'content' => 'Kenya has been dealing with the issue of water scarcity for decades. With a growing population, rising living costs, and a steadily rising poverty index, water scarcity means more suffering, particularly for women and girls, slower development, and increased health risks. Kilifi county is among the areas facing serious water shortages.

                According to the National Bureau of Statistics (KNBS), Kilifi county has a population of 1,453,787 people. For the past two consecutive years, the area has recorded minimal levels of rainfall leading to low crop yields, poor livestock production, and low levels of water. The county hit headlines before because of prolonged drought and fears of looming famine making them rely on support from the county and national governments, and humanitarian relief organizations. Therefore, improving access to water can improve the lives of people in Kilifi.
                
                Kenya Water and Sanitation Civil Society Network (KEWASNET), in collaboration with Sauti ya Pwani (SYP), hosted a community engagement session on the state of WASH in Magarini, Kanagoni, Kilifi County, on July 8th. At least 15 members of the community took part in the discussion. KEWASNET Head of Programmes, Vincent Ouma, KEWASNET Programmes Support Specialist, Public Health Officer from the Department of Health, Kilifi, Erick Diwani, technical assistant at the Kilifi County Department of Water, and radio presenter at Sauti Ya Pwani, Sammy Mwaura were in attendance. The show was taped and aired on the station on July 14th from 8:00 pm to 9:00 pm.',

            ],

        ];
        $this->db->table('blogs')->insertBatch($data);
    }
}
