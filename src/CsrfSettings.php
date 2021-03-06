<?php

namespace Rhubarb\Csrf;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Settings;

class CsrfSettings extends Settings
{
    public $domain = "";

    protected function initialiseDefaultValues()
    {
        parent::initialiseDefaultValues();

        $settings = Settings\WebsiteSettings::singleton();

        if ($settings->absoluteWebsiteUrl){
            $host = parse_url($settings->absoluteWebsiteUrl, PHP_URL_HOST);

            if ($host){
                $this->domain = $host;
            }
        } else {

            /**
             * @var WebRequest $request
             */
            $request = Application::current()->request();

            $host = parse_url($request->host, PHP_URL_HOST);

            if ($host){
                $this->domain = $host;
            } else {
                $this->domain = $request->host;
            }
        }
    }
}
