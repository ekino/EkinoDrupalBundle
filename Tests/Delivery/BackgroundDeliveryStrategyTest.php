<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Tests\Delivery;

use Ekino\Bundle\DrupalBundle\Delivery\BackgroundDeliveryStrategy;

/**
 * Tests the background delivery strategy class.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class BackgroundDeliveryStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the buildResponse() method.
     *
     * The disableContent() method on the Drupal instance should be called in any case.
     */
    public function testIsFound()
    {
        $drupal = $this->getMock('Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface');
        $drupal->expects($this->once())->method('disableResponse');

        $strategy = new BackgroundDeliveryStrategy();
        $strategy->buildResponse($drupal);
    }
}