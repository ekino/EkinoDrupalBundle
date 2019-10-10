<?php

namespace Ekino\Bundle\DrupalBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface;
use Ekino\Bundle\DrupalBundle\Drupal\InvalidStateMethodCallException;

/**
 * When Symfony throw a not found http exception, it response a drupal not found.
 *
 * @author Florent Denis <fdenis@ekino.com>
 */
class ExceptionListener
{
    /**
     * @var DrupalInterface
     */
    protected $drupal;

    /**
     * Constructor.
     *
     * @param DrupalInterface $drupal
     */
    public function __construct(DrupalInterface $drupal)
    {
        $this->drupal = $drupal;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $is404 = false;

        try {
            $is404 = $this->drupal->is404();
        }
        catch (InvalidStateMethodCallException $e) {}

        if (!$is404) {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {
            $this->drupalRender();

            $event->setResponse($this->drupal->getResponse());
        }
    }

    /**
     * Render drupal.
     */
    protected function drupalRender()
    {
        if ($this->drupal->isFound()) {
            $this->drupal->buildContent();
        }

        $this->drupal->render();
    }
}
