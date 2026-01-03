<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Vimeo extends BaseConfig
{
    /**
     * Vimeo API Access Token
     * Get this from Vimeo Developer settings
     */
    public string $accessToken = '';

    /**
     * Vimeo API Base URL
     */
    public string $apiUrl = 'https://api.vimeo.com';

    /**
     * Default embed settings
     */
    public array $embedSettings = [
        'autoplay' => false,
        'loop' => false,
        'muted' => false,
        'responsive' => true,
    ];

    /**
     * Domain restrictions (whitelist)
     * Add your domain here to restrict video access
     */
    public array $allowedDomains = [
        'localhost',
        'kewasnet.com',
    ];
}

