<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Entity;

class User extends HybridUser
{
    public $uid;

    public function getUid()
    {
        return $this->uid;
    }
}
