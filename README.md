Drupal Bundle by Ekino
======================

* THIS IS A WORK IN PROGRESS *

The bundle tries to deeply integrate Symfony2 with Drupal and Drupal with Symfony2. Of course this is done without
altering the Drupal's core.


Install
-------

### Download the symfony2 sandbox and the drupal code

### Install the files to have the following structure

    Symfony Sandbox Root
      - app
      - vendor
      - src
      - web ( drupal source code)

The ``web`` directory must be the document root and contains the drupal source code.

### Update the ``index.php`` file

This file "share" the container with Drupal so it is possible to reuse Symfony2's services from within Drupal. The
initialization process is always handled by Symfony2.

``` php
<?php
require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/bootstrap_cache.php.cache';
//require_once __DIR__.'/../app/AppCache.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('dev', true); //
$kernel->loadClassCache();
$kernel->boot();

// make the symfony container available from drupal file
global $container;

$container = $kernel->getContainer();

$kernel->handle(Request::createFromGlobals())->send();
```

### Configuration

Edit the symfony ``config.yml`` file and add the following lines :

    framework:
        # ... configuration options
        session:
            # ... configuration options
            auto_start:     false
            storage_id:     ekino.drupal.session

    ekino_drupal:
        root:         %kernel.root_dir%/../web
        strategy_id:  ekino.drupal.delivery_strategy.symfony

    # declare 2 required mapping definition used by drupal
    doctrine:
        dbal:
            default_connection: default
            connections:
                default:
                    driver:   %database_driver%
                    dbname:   %database_name%
                    user:     %database_user%
                    host:     %database_host%
                    password: %database_password%

                    mapping_types:
                        longblob: object
                        blob: object

The bundle comes with 2 delivery strategies :

* ekino.drupal.delivery_strategy.symfony : Drupal returns the response only if the page is not 404
* ekino.drupal.delivery_strategy.drupal  : Drupal always returns the response, even if the page is 404

Update Queries
--------------

``` sql
UPDATE users SET `email_canonical` = `mail`, `username_canonical` = `name`
```

Usage
-----

Symfony components can be used from within drupal :

``` php
<?php
function drupal_foo_function() {
    global $container;

    $result = $container->get('reusage_service')->foo();

    // do some stuff with $result
}
```

Limitations
-----------

* It is not possible to use Symfony native class to manage session as drupal initializes its own session handler
and there is no way to change this.
* requests must be served through the index.php as it is the default value in the .htaccess file and there is no
way to change the default script in drupal
