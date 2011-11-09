<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Drupal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Ekino\Bundle\DrupalBundle\Delivery\DeliveryStrategyInterface;

/**
 * The class retrieve a request and ask drupal to build the content
 *
 * See http://symfony.com/doc/current/book/internals.html#handling-requests
 *
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class DrupalRequestListener
{
    protected $drupal;

    protected $strategy;

    /**
     * @param DrupalInterface $drupal
     * @param \Ekino\Bundle\DrupalBundle\Delivery\DeliveryStrategyInterface $strategy
     */
    public function __construct(DrupalInterface $drupal, DeliveryStrategyInterface $strategy)
    {
        $this->drupal = $drupal;
        $this->strategy = $strategy;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @return mixed
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return false;
        }

        $this->drupal->defineState($event->getRequest());

        $this->strategy->buildResponse($this->drupal);

        $response = $this->drupal->getResponse();

        if ($this->drupal->hasResponse()) {
            $event->setResponse($response);
        }
    }
}