# Module.CsrfProtection
Provides a mechanism for CSRF protection

## Usage

Simply require the module using composer:

```
composer require rhubarbphp/module-csrfprotection
```

There are two types of validation provided

### Header validation

Simply call the `validateHeaders` method of the library to compare Origin and Referrer headers with the active request.

``` php
CsrfProtection::singleton()->validateHeaders($request);
```

$request should be the active WebRequest object. If you don't have a reference to it you can get it using

``` php
$request = Request::current();
```

This validation should be done for every POST request. It can also be done for GET requests, however it isn't recommended as it will fail on the first request a client makes to the site.

### Cookie validation

This approach should be used in conjunction with header validation and compares a posted value against a previously generated random token stored in a cookie on the client.

When you output a form tag include the CSRF cookie token:

```
$csrfProtector = CsrfProtection::singleton();

print '<input type="hidden" name="' . CsrfProtection::TOKEN_COOKIE_NAME . '" value="' . htmlentities($csrfProtector->getCookie()) . '" />';
```

When handling the post back, validate headers and the cookie:

```php
if ($request->server('REQUEST_METHOD') == 'POST'){
    CsrfProtection::singleton()->validateHeaders($request);
    CsrfProtection::singleton()->validateCookie($request);
}
```
