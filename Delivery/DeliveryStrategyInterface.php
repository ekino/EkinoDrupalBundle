<?php

namespace Ekino\Bundle\DrupalBundle\Delivery;

use Ekino\Bundle\DrupalBundle\Drupal\Drupal;

interface DeliveryStrategyInterface
{
    /**
     * @abstract
     * @param Ekino\Bundle\DrupalBundle\Drupal\Drupal $drupal
     */
    function buildResponse(Drupal $drupal);
}