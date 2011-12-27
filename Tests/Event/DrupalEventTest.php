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

use Ekino\Bundle\DrupalBundle\Event\DrupalEvent;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class DrupalEventTest extends \PHPUnit_Framework_TestCase
{
    public function testReference()
    {
        $value = array(
            'name' => 'Foo'
        );

        $event = new DrupalEvent();
        $event->addParameter($value);

        $v = &$event->getParameter(0);

        $v['name'] = 'Bar';

        $this->assertEquals('Bar', $value['name']);

        $value['name'] = 'Foo';

        $this->assertEquals('Foo', $v['name']);
    }
}