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
 * This strategy let's drupal render the all reponse stack, so symfony cannot response ...
 *
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class FullDrupalDeliveryStrategy implements DeliveryStrategyInterface
{
    /**
     * @param Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface $drupal
     */
    public function buildResponse(DrupalInterface $drupal)
    {
        if ($drupal->isFound()) {
            $drupal->buildContent();
        }

        $drupal->render();
    }
}