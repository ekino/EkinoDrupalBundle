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
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class DrupalEntityEvent extends Event
{
    protected $type;

    protected $entity;

    /**
     * @param array $parameters
     */
    public function __construct($type, $entity)
    {
        $this->type = $type;
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isType($name)
    {
        return $this->type === $name;
    }
}