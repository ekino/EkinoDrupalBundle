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

use Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;
use Ekino\Bundle\DrupalBundle\Drupal\Drupal;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class DrupalSessionStorage implements SessionStorageInterface
{
    protected $drupal;
    protected $userCache;

    /**
     * @param \Ekino\Bundle\DrupalBundle\Drupal\Drupal $drupal
     */
    public function __construct(Drupal $drupal)
    {
        $this->drupal = $drupal;
    }

    /**
     * Starts the session.
     *
     * @api
     */
    public function start()
    {
        global $user;

        $this->drupal->initialize();

        // cloning
        $this->userCache = new \stdClass();
        foreach (array('uid','cache','timestamp','access') as $clone) {
            $this->userCache->$clone = $user->$clone;
        }
    }

    /**
     * Returns the session ID
     *
     * @return mixed  The session ID
     *
     * @throws \RuntimeException If the session was not started yet
     *
     * @api
     */
    public function getId()
    {
        $this->drupal->initialize();

        return session_id();
    }

    /**
     * Reads data from this storage.
     *
     * The preferred format for a key is directory style so naming conflicts can be avoided.
     *
     * @param  string $key  A unique key identifying your data
     *
     * @return mixed Data associated with the key
     *
     * @throws \RuntimeException If an error occurs while reading data from this storage
     *
     * @api
     */
    public function read($key)
    {
        $this->drupal->initialize();

        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Removes data from this storage.
     *
     * The preferred format for a key is directory style so naming conflicts can be avoided.
     *
     * @param  string $key  A unique key identifying your data
     *
     * @return mixed Data associated with the key
     *
     * @throws \RuntimeException If an error occurs while removing data from this storage
     *
     * @api
     */
    public function remove($key)
    {
        $this->drupal->initialize();

        unset($_SESSION[$key]);
    }

    /**
     * Writes data to this storage.
     *
     * The preferred format for a key is directory style so naming conflicts can be avoided.
     *
     * @param  string $key   A unique key identifying your data
     * @param  mixed  $data  Data associated with your key
     *
     * @throws \RuntimeException If an error occurs while writing to this storage
     *
     * @api
     */
    public function write($key, $data)
    {
        global $user;

        $this->drupal->initialize();

        $_SESSION[$key] = $data;

        $user = $this->userCache;

        _drupal_session_write(session_id(), session_encode());
    }

    /**
     * Regenerates id that represents this storage.
     *
     * @param  Boolean $destroy Destroy session when regenerating?
     *
     * @return Boolean True if session regenerated, false if error
     *
     * @throws \RuntimeException If an error occurs while regenerating this storage
     *
     * @api
     */
    public function regenerate($destroy = false)
    {
        drupal_session_regenerate();
    }
}
