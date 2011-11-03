<?php

namespace Ekino\Bundle\DrupalBundle\Drupal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Ekino\Bundle\DrupalBundle\Delivery\DeliveryStrategyInterface;

/**
 * The class retrieve a request and ask drupal to build the content
 *
 * See http://symfony.com/doc/current/book/internals.html#handling-requests
 */
class DrupalRequestListener
{
    protected $drupal;

    protected $strategy;

    /**
     * @param Drupal $drupal
     */
    public function __construct(Drupal $drupal, DeliveryStrategyInterface $strategy)
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