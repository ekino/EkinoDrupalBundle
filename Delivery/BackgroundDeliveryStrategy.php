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
 * This strategy is used to let Symfony drive all the frontend and use Drupal in background.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class BackgroundDeliveryStrategy implements DeliveryStrategyInterface
{
    /**
     * @param DrupalInterface $drupal
     */
    public function buildResponse(DrupalInterface $drupal)
    {
        $drupal->disableResponse();
    }
}
