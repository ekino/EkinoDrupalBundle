<?php

namespace Ekino\Bundle\DrupalBundle\Delivery;

use Ekino\Bundle\DrupalBundle\Drupal\Drupal;


/**
 * This strategy let's drupal render the all reponse stack, so symfony cannot response ...
 */
class FullDrupalDeliveryStrategy implements DeliveryStrategyInterface
{
    /**
     * @param Ekino\Bundle\DrupalBundle\Drupal\Drupal $drupal
     */
    public function buildResponse(Drupal $drupal)
    {
        if ($drupal->isFound()) {
            $drupal->buildContent();
        }

        $drupal->render();
    }
}