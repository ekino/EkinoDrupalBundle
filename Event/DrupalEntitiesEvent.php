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
class DrupalEntitiesEvent extends Event
{
    protected $type;

    protected $entities;

    /**
     * @param array $parameters
     */
    public function __construct($type, &$entities)
    {
        $this->type     = $type;
        $this->entities =& $entities;
    }

    public function &getEntities()
    {
        return $this->entities;
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