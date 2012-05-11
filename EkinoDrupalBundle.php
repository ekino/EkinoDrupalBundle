<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class EkinoDrupalBundle extends Bundle
{

    /**
    * {@inheritdoc}
    */
    public function boot()
    {
        if (php_sapi_name() === 'cli' && !defined('EKINO_DRUSH_FROM')) {

            global $container;

            $container = $this->container;

            if (!defined('DRUSH_BASE_PATH')) {
                define('EKINO_DRUSH_FROM', 'symfony');

                $this->container->get('ekino.drupal')->initializeDrush();
            } else {
                define('EKINO_DRUSH_FROM', 'drupal');
            }
        }
    }
}
