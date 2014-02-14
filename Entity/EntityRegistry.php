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

use Ekino\Bundle\DrupalBundle\Drupal\DrupalInterface;

/**
 * Entity registry service
 *
 * @author Rémi Marseille <marseille@ekino.com>
 */
class EntityRegistry
{
    /**
     * @var DrupalInterface
     */
    private $drupal;

    /**
     * @var EntityRepository[]
     */
    private $repositories = array();

    /**
     * @var array
     */
    private $repositoriesMetadata = array();

    /**
     * Constructor
     *
     * @param DrupalInterface $drupal A Drupal instance
     */
    public function __construct(DrupalInterface $drupal)
    {
        $this->drupal = $drupal;
    }

    /**
     * Gets the entity controller
     *
     * @param string $entityType An entity type
     *
     * @return \DrupalDefaultEntityController
     */
    public function getController($entityType)
    {
        return $this->drupal->getEntityController($entityType);
    }

    /**
     * Adds metadata of a repository
     *
     * @param string      $class      A namespace of repository class
     * @param string      $entityType An entity type
     * @param string|null $bundle     A bundle name
     */
    public function addRepositoryMetadata($class, $entityType, $bundle)
    {
        $this->repositoriesMetadata[$this->computeKey($entityType, $bundle)] = array(
            'class'      => $class,
            'entityType' => $entityType,
            'bundle'     => $bundle,
        );
    }

    /**
     * Gets the entity repository
     *
     * @param string      $entityType An entity type
     * @param string|null $bundle     A bundle name
     *
     * @return EntityRepository
     */
    public function getRepository($entityType, $bundle = null)
    {
        $key = $this->computeKey($entityType, $bundle);

        if (!isset($this->repositories[$key])) {
            $class = isset($this->repositoriesMetadata[$key]) ? $this->repositoriesMetadata[$key]['class'] : 'Ekino\Bundle\DrupalBundle\Entity\EntityRepository';

            $this->repositories[$key] = new $class($entityType, $bundle);
        }

        return $this->repositories[$key];
    }

    /**
     * Computes the key
     *
     * @param string $entityType An entity type
     * @param string $bundle     A bundle name
     *
     * @return string
     */
    private function computeKey($entityType, $bundle)
    {
        // use @ separator to avoid collision
        return sprintf('%s@%s', $entityType, $bundle);
    }
}
