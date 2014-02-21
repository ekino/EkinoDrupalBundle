<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Event\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Adds a prefix to the name of Symfony tables
 *
 * @author Rémi Marseille <marseille@ekino.com>
 */
class TablePrefixSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var array
     */
    private $exclude;

    /**
     * Constructor
     *
     * @param string $prefix  The prefix of Symfony tables
     * @param array  $exclude An array of tables to exclude
     */
    public function __construct($prefix, array $exclude = array())
    {
        $this->prefix  = $prefix;
        $this->exclude = $exclude;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array('loadClassMetadata');
    }

    /**
     * Alters table name and associations
     *
     * @param LoadClassMetadataEventArgs $args
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();

        if (in_array($classMetadata->getTableName(), $this->exclude)) {
            return;
        }

        $classMetadata->setPrimaryTable(array(
            'name' => $this->prefix . $classMetadata->getTableName()
        ));

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }
    }
}
