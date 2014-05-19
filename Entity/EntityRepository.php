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

/**
 * Entity repository
 *
 * @author Rémi Marseille <marseille@ekino.com>
 */
class EntityRepository
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $bundle;

    /**
     * Constructor
     *
     * @param string $type   An entity type (like node)
     * @param string $bundle A bundle name (like page, article, ...)
     */
    public function __construct($type, $bundle = null)
    {
        $this->type   = $type;
        $this->bundle = $bundle;
    }

    /**
     * Finds all entities
     *
     * @return array
     */
    public function findAll()
    {
        return $this->findBy(array());
    }

    /**
     * Finds all published entities
     *
     * @return array
     */
    public function findAllPublished()
    {
        return $this->findBy(array(), array(array(
            'column' => 'status',
            'value'  => 1,
        )));
    }

    /**
     * Finds entities by given criteria
     *
     * @param array        $entityConditions   An array of entity conditions to filter results
     * @param array        $propertyConditions An array of property conditions to filter results
     * @param array        $fieldConditions    An array of field conditions to filter results
     * @param integer|null $offset             Offset used in query
     * @param integer|null $limit              Limit used in query
     *
     * @return array
     */
    public function findBy(array $entityConditions, array $propertyConditions = array(), array $fieldConditions = array(), $offset = null, $limit = null)
    {
        $query = $this->createQuery();

        foreach ($entityConditions as $crit) {
            $query->entityCondition($crit['name'], $crit['value'], array_key_exists('operator', $crit) ? $crit['operator'] : null);
        }

        foreach ($propertyConditions as $crit) {
            $query->propertyCondition($crit['column'], $crit['value'], array_key_exists('operator', $crit) ? $crit['operator'] : null);
        }

        foreach ($fieldConditions as $crit) {
            $query->fieldCondition($crit['field'], $crit['column'], $crit['value'], array_key_exists('operator', $crit) ? $crit['operator'] : null);
        }

        if (null !== $offset) {
            $query->range($offset);
        }

        if (null !== $limit) {
            $query->range($offset, $limit);
        }

        $result = $query->execute();

        return isset($result[$this->type]) ? entity_load($this->type, array_keys($result[$this->type])) : array();
    }

    /**
     * Finds one entity by a given identifier
     *
     * @param integer $id An entity identifier
     *
     * @return \stdClass|false
     */
    public function find($id)
    {
        return $this->findOneBy(array(array(
            'name'  => 'entity_id',
            'value' => $id,
        )));
    }

    /**
     * Finds one published entity by a given identifier
     *
     * @param integer $id An entity identifier
     *
     * @return \stdClass|false
     */
    public function findPublished($id)
    {
        return $this->findOnePublishedBy(array(array(
            'name'  => 'entity_id',
            'value' => $id,
        )));
    }

    /**
     * Finds one entity by given criteria
     *
     * @param array $entityConditions   An array of entity conditions to filter results
     * @param array $propertyConditions An array of property conditions to filter results
     * @param array $fieldConditions    An array of field conditions to filter results
     *
     * @return \stdClass|false
     */
    public function findOneBy(array $entityConditions, array $propertyConditions = array(), array $fieldConditions = array())
    {
        $entities = $this->findBy($entityConditions, $propertyConditions, $fieldConditions, null, 1);

        return reset($entities);
    }

    /**
     * Finds one published entity by given criteria
     *
     * @param array $entityConditions   An array of entity conditions to filter results
     * @param array $propertyConditions An array of property conditions to filter results
     * @param array $fieldConditions    An array of field conditions to filter results
     *
     * @return \stdClass|false
     */
    public function findOnePublishedBy(array $entityConditions, array $propertyConditions = array(), array $fieldConditions = array())
    {
        $propertyConditions = array_replace_recursive($propertyConditions, array(array(
            'column' => 'status',
            'value'  => 1,
        )));

        return $this->findOneBy($entityConditions, $propertyConditions, $fieldConditions);
    }

    /**
     * Creates a base entity field query
     *
     * @param string $bundle A bundle name
     *
     * @return \EntityFieldQuery
     */
    public function createQuery($bundle = null)
    {
        $query = new \EntityFieldQuery;
        $query->entityCondition('entity_type', $this->type);

        $bundle = $bundle ?: $this->bundle;

        if ($bundle) {
            $query->entityCondition('bundle', $bundle);
        }

        return $query;
    }
}
