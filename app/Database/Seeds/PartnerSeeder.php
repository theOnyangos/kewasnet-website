<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\PartnerModel as Model;

class PartnerSeeder extends Seeder
{
    public function run()
    {
        // Create partners object array
        $partners = [
            [
                'id' => 'partner--0000-0000-0000-000000000001',
                'partner_name' => 'Majizima',
                'partner_logo' => 'majizima.png',
                'partner_url' => 'http://majizima.org',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'ade0df0a-1c99-4155-becc-214a0eb77231',
                'partner_name' => 'Living Water International',
                'partner_logo' => 'living-water.png',
                'partner_url' => 'http://water.cc',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '04a6b8d8-a58c-48d4-a78b-90d6ae545f02',
                'partner_name' => 'Nosim',
                'partner_logo' => 'nosim.jpg',
                'partner_url' => 'https://www.google.com',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'b3de2cb2-c495-4bc6-bf6c-f975921e1fca',
                'partner_name' => 'Safe Water and AIDS Project (SWAP)',
                'partner_logo' => 'swap.jpg',
                'partner_url' => 'http://www.swapkenya.org/',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '9d0d4361-9296-44df-a0ce-99a08610786f',
                'partner_name' => 'Support for Tropical Initiatives in Poverty Alleviation (STIPA)',
                'partner_logo' => 'stipa.jpg',
                'partner_url' => 'http://www.stipakenya.org/',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '10b5ad20-17f0-4179-92f5-9f97de68e196',
                'partner_name' => 'The Kenya Red Cross Society (KRCS)',
                'partner_logo' => 'redcross.jpg',
                'partner_url' => 'http://www.kenyaredcross.org/',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '8a27b51f-248b-495a-a690-dd919c60d7f8',
                'partner_name' => 'Kenya Wetlands Forum',
                'partner_logo' => 'kwf.jpg',
                'partner_url' => 'http://www.kenyawetlandforum.org/ ',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'fe4596ff-e853-4aeb-b33b-ba214a75e246',
                'partner_name' => 'OGRA Foundation',
                'partner_logo' => 'ogra.jpg',
                'partner_url' => 'http://www.kenyawetlandforum.org/',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '34b438b6-0442-4ca8-8c14-d5414e1369ca',
                'partner_name' => 'WSUP',
                'partner_logo' => 'swup.jpg',
                'partner_url' => 'http://www.wsup.org/',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 'a78dbbc7-05b0-469e-98c5-bb714fe1cea4',
                'partner_name' => 'Neighbors Initiative Alliance (NIA)',
                'partner_logo' => 'nia.jpg',
                'partner_url' => 'http://www.nia.org/ ',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => '019f853a-8aab-467b-b0e4-d9e29645bb61',
                'partner_name' => 'Dupoto-e-Maa (Olkejuado Pastoralists Development Organization)',
                'partner_logo' => 'dupoto.jpg',
                'partner_url' => 'http://www.dupotoemaa.org/',
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Create a new partners model object
        $model = new Model();
        $model->insertBatch($partners);
    }
}
