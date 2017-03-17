CHANGELOG
=========

A [BC BREAK] means the update will break the project for many reasons:

* new mandatory configuration
* new dependencies
* class refactoring


### 2017-03-17

* Prevents to close the user session too quickly in order to allow Drupal to write in.
It fixes the reset password process.
* Adds psr/log to fix the deprecation of LoggerInterface.

### 2014-05-19

* [BC BREAK] Third argument of EntityRepository::findBy method is now $fieldConditions.

### 2014-05-18

* [BC BREAK] Second argument of EntityRepository::findBy method is now $propertyConditions.

### 2014-05-18

* Added some finders in EntityRepository class to retrieve some published contents.
