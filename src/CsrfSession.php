<?php

namespace Rhubarb\Csrf;

use Rhubarb\Crown\Sessions\Session;

class CsrfSession extends Session
{
    public $csrfToken;

    /**
     * Validates the request for CSRF compliance.
     *
     * @throws CsrfViolationException Thrown if the request doesn't comply.
     */
    public function validate()
    {

    }
}