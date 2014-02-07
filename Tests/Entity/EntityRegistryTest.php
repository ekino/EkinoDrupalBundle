<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Tests\Entity;

use Ekino\Bundle\DrupalBundle\Entity\EntityRegistry;

/**
 * Tests the entity registry service
 *
 * @author Rémi Marseille <marseille@ekino.com>
 */
class EntityRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityRegistry
     */
    private $entityRegistry;

    /**
     * Tests to add a repository metadata
     */
    public function testAddRepositoryMetadata()
    {
        $property = new \ReflectionProperty($this->entityRegistry, 'repositoriesMetadata');
        $property->setAccessible(true);

        $this->assertTrue(is_array($property->getValue($this->entityRegistry)));
        $this->assertCount(0, $property->getValue($this->entityRegistry));

        $this->entityRegistry->addRepositoryMetadata('Ekino\Bundle\DrupalBundle\Tests\Stub\FooEntityRepository', 'node', 'page');

        $this->assertCount(1, $property->getValue($this->entityRegistry));
        $expected = array('node@page' => array(
            'class'      => 'Ekino\Bundle\DrupalBundle\Tests\Stub\FooEntityRepository',
            'entityType' => 'node',
            'bundle'     => 'page',
        ));
        $this->assertEquals($expected, $property->getValue($this->entityRegistry));
    }

    /**
     * Tests whether the default entity repository is returned
     */
    public function testGetDefaultEntityRepository()
    {
        $this->assertInstanceOf('Ekino\Bundle\DrupalBundle\Entity\EntityRepository', $this->entityRegistry->getRepository('node', 'page'));

        $this->entityRegistry->addRepositoryMetadata('Ekino\Bundle\DrupalBundle\Tests\Stub\FooEntityRepository', 'node', 'page');

        $this->assertNotInstanceOf('Ekino\Bundle\DrupalBundle\Tests\Stub\FooEntityRepository', $this->entityRegistry->getRepository('node', 'page'));
    }

    /**
     * Tests whether the configured entity repository is returned
     */
    public function testGetConfiguredEntityRepository()
    {
        $this->entityRegistry->addRepositoryMetadata('Ekino\Bundle\DrupalBundle\Tests\Stub\FooEntityRepository', 'node', 'page');

        $this->assertInstanceOf('Ekino\Bundle\DrupalBundle\Tests\Stub\FooEntityRepository', $this->entityRegistry->getRepository('node', 'page'));
    }

    /**
     * Tests the computed key
     */
    public function testComputeKey()
    {
        $method = new \ReflectionMethod($this->entityRegistry, 'computeKey');
        $method->setAccessible(true);

        $this->assertEquals('node@page', $method->invoke($this->entityRegistry, 'node', 'page'));
    }

    /**
     * Initializes the entity registry
     */
    protected function setUp()
    {
        $drupal = $this->getMock('Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface');

        $this->entityRegistry = new EntityRegistry($drupal);
    }

    /**
     * Cleanups the entity registry
     */
    protected function tearDown()
    {
        unset($this->entityRegistry);
    }
}
