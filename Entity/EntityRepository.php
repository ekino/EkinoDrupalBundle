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
     * Finds entities by given criteria
     *
     * @todo Implements other methods like propertyCondition, fieldCondition, ...
     *
     * @param array        $criteria An array of criteria to filter results
     * @param integer|null $offset   Offset used in query
     * @param integer|null $limit    Limit used in query
     *
     * @return array
     */
    public function findBy(array $criteria, $offset = null, $limit = null)
    {
        $query = $this->createQuery();

        foreach ($criteria as $crit) {
            $query->entityCondition($crit['name'], $crit['value'], array_key_exists('operator', $crit) ? $crit['operator'] : null);
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
     * Finds one entity by given criteria
     *
     * @param array $criteria An array of criteria to filter results
     *
     * @return \stdClass|false
     */
    public function findOneBy(array $criteria)
    {
        $entities = $this->findBy($criteria, null, 1);

        return reset($entities);
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
