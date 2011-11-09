<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Delivery;

use Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface;

/**
 * This strategy let's a change to symfony to sent its own response
 *
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class FullSymfonyDeliveryStrategy implements DeliveryStrategyInterface
{
    /**
     * @param Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface $drupal
     */
    public function buildResponse(DrupalInterface $drupal)
    {
        if ($drupal->is404()) {
            $drupal->disableResponse();

            return;
        }

        if ($drupal->isFound()) {
            $drupal->buildContent();
        }

        $drupal->render();
    }
}