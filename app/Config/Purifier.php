<?php namespace Config;

use CodeIgniter\Config\BaseService;
use HTMLPurifier;
use HTMLPurifier_Config;

class Purifier extends BaseService
{
    public static function getPurifier()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,em,u,ul,ol,li,a[href|title],blockquote');
        $config->set('AutoFormat.AutoParagraph', true);
        $config->set('AutoFormat.Linkify', true);
        
        return new HTMLPurifier($config);
    }
}