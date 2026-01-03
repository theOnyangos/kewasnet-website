<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\TaskIcon;

class TaskIconSeeder extends Seeder
{
    public function run()
    {
        // Task icon data
        $taskIcons = [
            [
                'name' => 'inbox',
                'icon' => '<img class="svg" src="<?= base_url() ?>backend/img/svg/inbox.svg" alt="inbox" />',
            ],
            [
                'id' => '241d12e9-0e3f-45f9-9c43-091cfd984c21',
                'name' => 'upload',
                'icon' => '<img class="svg" src="<?= base_url() ?>backend/img/svg/upload.svg" alt="upload" />',
            ],
            [
                'id' => 'f398e1ec-7b97-4285-a931-1c5576f2b383',
                'name' => 'login',
                'icon' => '<img class="svg" src="<?= base_url() ?>backend/img/svg/log-in.svg" alt="log-in" />',
            ],
            [
                'id' => 'eb7af533-008c-4f09-a59f-483715e80719',
                'name' => 'atSign',
                'icon' => '<img class="svg" src="<?= base_url() ?>backend/img/svg/at-sign.svg" alt="at-sign" />',
            ],
            [
                'id' => '309c7153-4750-46fb-9ecf-a880f2548291',
                'name' => 'heart',
                'icon' => '<img class="svg" src="<?= base_url() ?>backend/img/svg/heart.svg" alt="heart" />',
            ],
            [
                'id' => 'a8925497-8f04-4403-8296-56ae8b1be3f4',
                'name' => 'messageSquare',
                'icon' => '<img class="svg" src="<?= base_url() ?>backend/img/svg/message-square.svg" alt="message-square" />',
            ]
        ];

        // Insert to the database
        $taskIconModel = new TaskIcon();
        $taskIconModel->insertBatch($taskIcons);
    }
}
