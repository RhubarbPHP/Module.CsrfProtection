<?php

namespace Rhubarb\Csrf\Tests;

use Codeception\Specify;
use Rhubarb\Crown\Application;
use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Settings\WebsiteSettings;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Csrf\CsrfSettings;

class CsrfSettingsTest extends RhubarbTestCase
{
    use Specify;

    public function testSettings()
    {
        $this->beforeSpecify(function(){
            $this->setUp();
        });

        $this->specify("Domain defaults to HTTP_HOST", function(){
            unset($_SERVER["SERVER_NAME"]);
            $_SERVER["HTTP_HOST"] = "abc123.com";

            Application::current()->setCurrentRequest(new WebRequest());

            $settings = CsrfSettings::singleton();

            verify($settings->domain)->equals("abc123.com");
        });

        $this->specify("Domain defaults to SERVER_NAME", function(){
            unset($_SERVER["HTTP_HOST"]);
            $_SERVER["SERVER_NAME"] = "def234.com";

            Application::current()->setCurrentRequest(new WebRequest());

            $settings = CsrfSettings::singleton();

            verify($settings->domain)->equals("def234.com");
        });

        $this->specify("Domain defaults to WebsiteSettings in preference", function(){
            unset($_SERVER["HTTP_HOST"]);
            $_SERVER["SERVER_NAME"] = "def234.com";

            WebsiteSettings::singleton()->absoluteWebsiteUrl = "http://testsite.com/";

            Application::current()->setCurrentRequest(new WebRequest());

            $settings = CsrfSettings::singleton();

            verify($settings->domain)->equals("testsite.com");
        });
    }
}
