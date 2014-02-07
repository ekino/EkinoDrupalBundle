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

/**
 * Tests the entity repository class
 *
 * @author Rémi Marseille <marseille@ekino.com>
 */
class EntityRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests findAll method
     */
    public function testFindAll()
    {
        $repository = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')
            ->setConstructorArgs(array('node'))
            ->setMethods(array('findBy'))
            ->getMock();

        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo(array()));

        $repository->findAll();
    }

    /**
     * Tests a very simple call to findBy method
     */
    public function testSimpleFindBy()
    {
        $repository = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')
            ->setConstructorArgs(array('node'))
            ->setMethods(array('createQuery'))
            ->getMock();

        $query = $this->getMockEntityFieldQuery();

        $repository
            ->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($query));

        foreach (array('entityCondition', 'range') as $method) {
            $query
                ->expects($this->never())
                ->method($method);
        }

        $query
            ->expects($this->once())
            ->method('execute');

        $repository->findBy(array());
    }

    /**
     * Tests the findBy method with criteria
     */
    public function testFindByWithCriteria()
    {
        $repository = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')
            ->setConstructorArgs(array('node'))
            ->setMethods(array('createQuery'))
            ->getMock();

        $query = $this->getMockEntityFieldQuery();

        $repository
            ->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($query));

        $query
            ->expects($this->at(0))
            ->method('entityCondition')
            ->with($this->equalTo('entity_type'), $this->equalTo('node'), $this->equalTo(null));

        $query
            ->expects($this->at(1))
            ->method('entityCondition')
            ->with($this->equalTo('bundle'), $this->equalTo('page'), $this->equalTo('='));

        $repository->findBy(array(
            array('name' => 'entity_type', 'value' => 'node'),
            array('name' => 'bundle',      'value' => 'page', 'operator' => '='),
        ));
    }

    /**
     * Tests the findBy method with offset
     */
    public function testFindByWithOffset()
    {
        $repository = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')
            ->setConstructorArgs(array('node'))
            ->setMethods(array('createQuery'))
            ->getMock();

        $query = $this->getMockEntityFieldQuery();

        $repository
            ->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($query));

        $query
            ->expects($this->once())
            ->method('range')
            ->with($this->equalTo(0));

        $repository->findBy(array(), 0);
    }

    /**
     * Tests the findBy method with limit
     */
    public function testFindByWithLimit()
    {
        $repository = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')
            ->setConstructorArgs(array('node'))
            ->setMethods(array('createQuery'))
            ->getMock();

        $query = $this->getMockEntityFieldQuery();

        $repository
            ->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($query));

        $query
            ->expects($this->once())
            ->method('range')
            ->with($this->equalTo(null), $this->equalTo(10));

        $repository->findBy(array(), null, 10);
    }

    /**
     * Tests the findBy method with range
     */
    public function testFindByWithRange()
    {
        $repository = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')
            ->setConstructorArgs(array('node'))
            ->setMethods(array('createQuery'))
            ->getMock();

        $query = $this->getMockEntityFieldQuery();

        $repository
            ->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($query));

        $query
            ->expects($this->at(0))
            ->method('range')
            ->with($this->equalTo(0));

        $query
            ->expects($this->at(1))
            ->method('range')
            ->with($this->equalTo(0), $this->equalTo(10));

        $repository->findBy(array(), 0, 10);
    }

    /**
     * Tests the find method
     */
    public function testFind()
    {
        $repository = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')
            ->setConstructorArgs(array('node'))
            ->setMethods(array('findOneBy'))
            ->getMock();

        $repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(array(array(
                'name'  => 'entity_id',
                'value' => 10,
            ))));

        $repository->find(10);
    }

    /**
     * Tests the findOneBy method
     */
    public function testFindOneBy()
    {
        $repository = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Entity\EntityRepository')
            ->setConstructorArgs(array('node'))
            ->setMethods(array('findBy'))
            ->getMock();

        $criteria = array(
            array('name' => 'entity_type', 'value' => 'node'),
            array('name' => 'bundle',      'value' => 'page'),
        );

        $repository
            ->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo($criteria), $this->equalTo(null), $this->equalTo(1))
            ->will($this->returnValue(array()));

        $repository->findOneBy($criteria);
    }

    /**
     * Gets a mock of entity field query
     *
     * @return object
     */
    private function getMockEntityFieldQuery()
    {
        return $this->getMock('Ekino\Bundle\DrupalBundle\Tests\Stub\EntityFieldQueryInterface');
    }
}
