<?php

namespace Rhubarb\Csrf\Tests;

use Codeception\Specify;
use Rhubarb\Crown\Application;
use Rhubarb\Crown\Http\HttpResponse;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Settings\WebsiteSettings;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Csrf\CsrfProtection;
use Rhubarb\Csrf\CsrfSettings;
use Rhubarb\Csrf\CsrfViolationException;

class CsrfProtectionTest extends RhubarbTestCase
{
    use Specify;

    public function testValidationOfHeaders()
    {
        $this->specify("Origin header can match", function(){
            $request = new WebRequest();
            $request->host = "test.com";
            $request->headerData["origin"] = "http://test.com";

            Application::current()->setCurrentRequest($request);

            $protection = CsrfProtection::singleton();
            $protection->validateHeaders($request);
        });

        $this->specify("Origin header must match", function(){
            $request = new WebRequest();
            $request->host = "test.com";
            $request->headerData["origin"] = "http://test2.com";

            Application::current()->setCurrentRequest($request);

            $protection = CsrfProtection::singleton();
            $protection->validateHeaders($request);
        }, ['throws' => CsrfViolationException::class]);

        $this->specify("Referrer header can match", function(){
            $request = new WebRequest();
            $request->host = "test.com";
            $request->headerData["referrer"] = "http://test.com";

            Application::current()->setCurrentRequest($request);

            $protection = CsrfProtection::singleton();
            $protection->validateHeaders($request);
        });

        $this->specify("If present Referrer must match", function(){
            $request = new WebRequest();
            $request->host = "test.com";
            $request->headerData["origin"] = "http://test.com";

            Application::current()->setCurrentRequest($request);

            $protection = CsrfProtection::singleton();
            $protection->validateHeaders($request);

            $request->headerData["referrer"] = "http://test2.com";

            Application::current()->setCurrentRequest($request);

            $protection = CsrfProtection::singleton();
            $protection->validateHeaders($request);
        }, ['throws' => CsrfViolationException::class]);

        $this->specify("Referrer or origin must be present", function(){
            $request = new WebRequest();
            $request->host = "test.com";

            Application::current()->setCurrentRequest($request);

            $protection = CsrfProtection::singleton();
            $protection->validateHeaders($request);

        }, ['throws' => CsrfViolationException::class]);
    }

    public function testDoubleCookieMethod()
    {
        $this->specify('Cookie and request variable must match', function(){
            $request = new WebRequest();
            $request->host = "test.com";
            $request->headerData["origin"] = "http://test.com";
            $request->cookieData["csrf_tk"] = uniqid();
            $request->postData["csrf_tk"] = $request->cookieData["csrf_tk"];

            Application::current()->setCurrentRequest($request);

            $protection = CsrfProtection::singleton();
            $protection->validateCookie($request);

            $request->postData["csrf_tk"] = uniqid();

            $protection = CsrfProtection::singleton();
            $protection->validateCookie($request);
        }, ['throws' => CsrfViolationException::class]);
    }

    public function testCookieCreatedAndSet()
    {
        $this->beforeSpecify(function(){
            Application::current()->container()->clearSingleton(CsrfProtection::class);
            WebsiteSettings::singleton()->absoluteWebsiteUrl = "http://www.test.com";
        });

        $this->specify('Cookie is returned', function(){
            $protection = CsrfProtection::singleton();
            $cookie = $protection->getCookie();

            verify($cookie)->notNull();
        });

        $this->specify('Cookie is static', function(){
            $protection = CsrfProtection::singleton();
            $cookieA = $protection->getCookie();
            $cookieB = $protection->getCookie();

            verify($cookieA)->equals($cookieB);
        });

        $this->specify('Cookie is random', function(){
            $protection = CsrfProtection::singleton();
            $cookieA = $protection->getCookie();

            Application::current()->container()->clearSingleton(CsrfProtection::class);

            $protection = CsrfProtection::singleton();
            $cookieB = $protection->getCookie();

            verify($cookieA)->notEquals($cookieB);
        });

        $this->specify('Cookie header is sent', function(){

            Application::current()->setCurrentRequest(new WebRequest());

            $protection = CsrfProtection::singleton();
            $cookieA = $protection->getCookie();

            $request = Request::current();

            verify($request->cookie(CsrfProtection::TOKEN_COOKIE_NAME))->equals($cookieA);
        });

        $this->specify('Cookie header is not changed if submitted in the request', function(){

            $request = new WebRequest();
            $request->cookieData[CsrfProtection::TOKEN_COOKIE_NAME] = "abc123";
            Application::current()->setCurrentRequest($request);

            $protection = CsrfProtection::singleton();
            $cookieA = $protection->getCookie();

            verify($cookieA)->equals("abc123");
            verify($request->cookie(CsrfProtection::TOKEN_COOKIE_NAME))->equals($cookieA);
        });
    }
}