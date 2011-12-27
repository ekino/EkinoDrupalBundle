<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 *
 *
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class DrupalEvent extends Event
{
    protected $parameters = array();

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * @throws \RuntimeException
     * @param $position
     * @return
     */
    public function &getParameter($position)
    {
        if (!$this->hasParameter($position)) {
            throw new \RuntimeException('Invalid parameter');
        }

        return $this->parameters[$position];
    }

    /**
     * @param $position
     * @return bool
     */
    public function hasParameter($position)
    {
        return isset($this->parameters[$position]);
    }

    /**
     * @param $reference
     * @return DrupalEvent
     */
    public function addParameter(&$reference)
    {
        $this->parameters[] =& $reference;

        return $this;
    }
}