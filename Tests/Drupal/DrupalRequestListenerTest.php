<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Tests\Drupal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Ekino\Bundle\DrupalBundle\Delivery\DeliveryStrategyInterface;
use Ekino\Bundle\DrupalBundle\Drupal\DrupalRequestListener;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class DrupalRequestListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testNonMasterRequest()
    {
        $kernel     = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $drupal = $this->getMock('Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface');
        $strategy = $this->getMock('Ekino\Bundle\DrupalBundle\Delivery\DeliveryStrategyInterface');

        $request    = new Request;
        $event      = new GetResponseEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);
        $listener   = new DrupalRequestListener($drupal, $strategy);

        $this->assertFalse($listener->onKernelRequest($event));
    }

    public function testWithoutResponse()
    {
        $kernel     = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');

        $drupal = $this->getMock('Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface');
        $drupal->expects($this->once())->method('defineState');
        $drupal->expects($this->once())->method('hasResponse')->will($this->returnValue(false));

        $strategy = $this->getMock('Ekino\Bundle\DrupalBundle\Delivery\DeliveryStrategyInterface');
        $strategy->expects($this->once())->method('buildResponse');

        $request    = new Request;
        $event      = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $listener   = new DrupalRequestListener($drupal, $strategy);

        $listener->onKernelRequest($event);

        $this->assertFalse($event->hasResponse());
    }

    public function testWithResponse()
    {
        $kernel     = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $drupal = $this->getMock('Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface');
        $drupal->expects($this->once())->method('defineState');
        $drupal->expects($this->once())->method('hasResponse')->will($this->returnValue(true));
        $drupal->expects($this->once())->method('getResponse')->will($this->returnValue(new Response));

        $strategy = $this->getMock('Ekino\Bundle\DrupalBundle\Delivery\DeliveryStrategyInterface');
        $strategy->expects($this->once())->method('buildResponse');

        $request    = new Request;
        $event      = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $listener   = new DrupalRequestListener($drupal, $strategy);

        $listener->onKernelRequest($event);

        $this->assertTrue($event->hasResponse());
    }
}