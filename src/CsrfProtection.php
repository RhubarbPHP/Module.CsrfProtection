<?php

namespace Rhubarb\Csrf;

use Rhubarb\Crown\DependencyInjection\SingletonTrait;
use Rhubarb\Crown\Http\HttpResponse;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;

class CsrfProtection
{
    use SingletonTrait;

    const TOKEN_COOKIE_LENGTH = 16;
    const TOKEN_COOKIE_NAME = 'csrf_tk';

    protected function __construct()
    {

    }

    /**
     * Validates that a request headers meets CSRF requirements.
     *
     * @param WebRequest $request
     * @throws CsrfViolationException Thrown if the request does not validate on headers.
     */
    public function validateHeaders(WebRequest $request)
    {
        $settings = CsrfSettings::singleton();

        $headersValid = false;

        $referrerDomain = $this->getHost($request->header("Referrer", ""));

        if ($referrerDomain != "") {
            if ($referrerDomain == $settings->domain){
                $headersValid = true;
            }
        } else {
            if ($this->getHost($request->header("Origin")) == $settings->domain) {
                $headersValid = true;
            }
        }

        if (!$headersValid){
            throw new CsrfViolationException();
        }
    }

    /**
     * Validates if the csrf_tk cookie matches the posted value.
     */
    public function validateCookie(WebRequest $request)
    {
        if ($request->cookie(self::TOKEN_COOKIE_NAME) != $request->post(self::TOKEN_COOKIE_NAME)) {
            throw new CsrfViolationException();
        }
    }

    private $currentCookie;

    /**
     * Returns the current cookie being used for CSRF or generates one if one doesn't exist.
     */
    public function getCookie()
    {
        $request = Request::current();

        if ($request instanceof WebRequest){
            $existingCookie = $request->cookie(self::TOKEN_COOKIE_NAME, false);

            if ($existingCookie){
                $this->currentCookie = $existingCookie;
            }
        }

        if (!$this->currentCookie) {
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ012345689.;,!$%^&*()-=+";
            $cookie = "";
            for ($x = 0; $x < self::TOKEN_COOKIE_LENGTH; $x++) {
                $rand = rand(0, strlen($chars) - 1);
                $char = $chars[$rand];

                if (rand(0, 1) == 1) {
                    $char = strtolower($char);
                }

                $cookie .= $char;
            }

            $this->currentCookie = $cookie;

            $settings = CsrfSettings::singleton();

            HttpResponse::setCookie(self::TOKEN_COOKIE_NAME, $cookie, 0, '/', $settings->domain);
        }

        return $this->currentCookie;
    }

    private function getHost($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }
}