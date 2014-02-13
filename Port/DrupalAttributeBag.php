<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Port;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

/**
 * @author Florent Denis <fdenis@ekino.com>
 */
class DrupalAttributeBag implements AttributeBagInterface, \IteratorAggregate, \Countable
{
    /**
     * @var string
     */
    private $storageKey;

    /**
     * Constructor.
     *
     * @param string $storageKey The key used to store flashes in the session.
     */
    public function __construct($storageKey = '_sf2_attributes')
    {
        $this->storageKey = $storageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'attributes';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array &$attributes)
    {
        $_SESSION[$this->storageKey] = &$attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageKey()
    {
        return $this->storageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return array_key_exists($name, $_SESSION[$this->storageKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        return array_key_exists($name, $_SESSION[$this->storageKey]) ? $_SESSION[$this->storageKey][$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $_SESSION[$this->storageKey][$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $_SESSION[$this->storageKey];
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $attributes)
    {
        $_SESSION[$this->storageKey] = array();
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $retval = null;
        if (array_key_exists($name, $_SESSION[$this->storageKey])) {
            $retval = $_SESSION[$this->storageKey][$name];
            unset($_SESSION[$this->storageKey][$name]);
        }

        return $retval;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $return = $_SESSION[$this->storageKey];
        $_SESSION[$this->storageKey] = array();

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($_SESSION[$this->storageKey]);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($_SESSION[$this->storageKey]);
    }
}