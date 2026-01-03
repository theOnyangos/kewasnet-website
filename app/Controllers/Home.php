<?php

namespace App\Controllers;

use App\Models\PartnerModel as Partner;
use App\Models\YoutubeLinkModel;
use App\Libraries\Mailer;

class Home extends BaseController
{
    protected YoutubeLinkModel $youtubeLinkModel;

    public function __construct()
    {
        $this->youtubeLinkModel = new YoutubeLinkModel();
    }

    public function index(): string
    {
        $youtubeLinks = $this->youtubeLinkModel->getYoutubeLinks();

        $partner = new Partner();
        $data = [
            'title' => 'KEWASNET - Welcome to the Kenya Water and Sanitation Civil Society Network website.',
            'description' => 'The Kenya Water and Sanitation Civil Society Network is the National Network of Water Civil Society Organizations in Kenya.',
            'partners' => $partner->getAllPartners(),
            'youtubeLinks' => $youtubeLinks,
        ];

        return view('frontend/pages/home/index', $data);
    }

    public function sendMail()
    {
        // Queue email
        $subject = 'Welcome to KEWASNET';
        $message = "This is a sample test mail";
        $from = env('EMAIL_FROM_ADDRESS');
        $fromName = env('EMAIL_FROM_NAME');
        $to = "otienodennis29@gmail.com"; // mweselijames@gmail.com

        $emailQueueModel = new \App\Models\EmailQueue();

        if ($emailQueueModel->queueEmail($to, $subject, $message, null, $from, $fromName)) {
            return true;
        } else {
            log_message('error', 'Failed to queue test email to: ' . $to);
            return false;
        }
    }
}
