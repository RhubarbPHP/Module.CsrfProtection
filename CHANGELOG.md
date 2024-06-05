# CHANGELOG

### 1.1.2

* Added:    validateUrlParameterToken method to validate a csrf token URL parameter. Used for GET requests, where there is no form to include the csrf token in.

### 1.1.1

* Added:    csrf_tk Cookie now sent with secure flag if on SSL.

### 1.1.0

* Added:    Support for multiple domains by passing an array to $domain

### 1.0.5

* Changed:  Changed the csrf_tk token to be HTTP only

### 1.0.4

* Fixed:    Reversed port support - ports were not considered originally and this change broke sites specifying reference domains without a port.

### 1.0.3

* Added:    Added Port support 

### 1.0.2   

* Fixed:    Fixed issue where the domain was not being set correctly

### 1.0.1

* Fixed:    Changed to use RFC mispelling of Referrer

### 1.0.0

* Added:    Cookie method of CSRF

