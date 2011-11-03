<?php

namespace Ekino\Bundle\DrupalBundle\Delivery;

use Ekino\Bundle\DrupalBundle\Drupal\Drupal;

/**
 * This strategy let's a change to symfony to sent its own response
 */
class FullSymfonyDeliveryStrategy implements DeliveryStrategyInterface
{
    /**
     * @param Ekino\Bundle\DrupalBundle\Drupal\Drupal $drupal
     */
    public function buildResponse(Drupal $drupal)
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