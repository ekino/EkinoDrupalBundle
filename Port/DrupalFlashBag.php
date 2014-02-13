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

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @author Florent Denis <fdenis@ekino.com>
 */
class DrupalFlashBag implements FlashBagInterface, \IteratorAggregate, \Countable
{
    /**
     * The storage key for flashes in the session
     *
     * @var string
     */
    private $storageKey;

    /**
     * Constructor.
     *
     * @param string $storageKey The key used to store flashes in the session.
     */
    public function __construct($storageKey = '_sf2_flashes')
    {
        $this->storageKey = $storageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flashes';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array &$flashes)
    {
        $_SESSION[$this->storageKey] = &$flashes;
    }

    /**
     * {@inheritdoc}
     */
    public function add($type, $message)
    {
        $_SESSION[$this->storageKey][$type][] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function peek($type, array $default =array())
    {
        return $this->has($type) ? $_SESSION[$this->storageKey][$type] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function peekAll()
    {
        return $_SESSION[$this->storageKey];
    }

    /**
     * {@inheritdoc}
     */
    public function get($type, array $default = array())
    {
        if (!$this->has($type)) {
            return $default;
        }

        $return = $_SESSION[$this->storageKey][$type];

        unset($_SESSION[$this->storageKey][$type]);

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $return = $this->peekAll();
        $_SESSION[$this->storageKey] = array();

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function set($type, $messages)
    {
        $_SESSION[$this->storageKey][$type] = (array) $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function setAll(array $messages)
    {
        $_SESSION[$this->storageKey] = $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function has($type)
    {
        return array_key_exists($type, $_SESSION[$this->storageKey]) && $_SESSION[$this->storageKey][$type];
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return array_keys($_SESSION[$this->storageKey]);
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
    public function clear()
    {
        return $this->all();
    }

    /**
     * Returns an iterator for flashes.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->all());
    }

    /**
     * Returns the number of flashes.
     *
     * This method does not work.
     *
     * @deprecated in 2.2, removed in 2.3
     * @see https://github.com/symfony/symfony/issues/6408
     *
     * @return int The number of flashes
     */
    public function count()
    {
        trigger_error(sprintf('%s() is deprecated since 2.2 and will be removed in 2.3', __METHOD__), E_USER_DEPRECATED);

        return count($_SESSION[$this->storageKey]);
    }
}
