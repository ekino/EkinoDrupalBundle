<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Tests\Stub;

/**
 * Entity field query stub
 *
 * @author Rémi Marseille <marseille@ekino.com>
 */
interface EntityFieldQueryInterface
{
    /**
     * @see \EntityFieldQuery
     */
    public function entityCondition();

    /**
     * @see \EntityFieldQuery
     */
    public function range();

    /**
     * @see \EntityFieldQuery
     */
    public function execute();
}
