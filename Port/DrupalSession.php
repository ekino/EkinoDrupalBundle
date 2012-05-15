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

use Symfony\Component\HttpFoundation\Session;

/**
 * Session for drupal.
 *
 * @author Florent Denis <florent.denis@fullsix.com>
 */
class DrupalSession extends Session
{
    /**
     * Starts the session storage.
     *
     * @api
     */
    public function start()
    {
        if (true === $this->started) {
            return;
        }

        parent::start();

        $attributes = $this->storage->read('_symfony2');

        if (isset($attributes['attributes'])) {
            if (!isset($attributes['oldFlashes'])) {
                $attributes['oldFlashes'] = array();
            }

            $_SESSION['_symfony2']['flashes'] = array_diff_key($attributes['flashes'], $attributes['oldFlashes']);
            // flag current flash messages to be removed at shutdown
            $_SESSION['_symfony2']['oldFlashes'] = $attributes['flashes'];
        }
    }

    /**
     * Checks if an attribute is defined.
     *
     * @param string $name The attribute name
     *
     * @return Boolean true if the attribute is defined, false otherwise
     *
     * @api
     */
    public function has($name)
    {
        $this->start();

        return isset($_SESSION['_symfony2']['attributes'][$name]);
    }

    /**
     * Returns an attribute.
     *
     * @param string $name    The attribute name
     * @param mixed  $default The default value
     *
     * @return mixed
     *
     * @api
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $_SESSION['_symfony2']['attributes'][$name] : $default;
    }

    /**
     * Sets an attribute.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @api
     */
    public function set($name, $value)
    {
        $this->start();

        $_SESSION['_symfony2']['attributes'][$name] = $value;
    }

    /**
     * Returns attributes.
     *
     * @return array Attributes
     *
     * @api
     */
    public function all()
    {
        return $_SESSION['_symfony2']['attributes'];
    }

    /**
     * Sets attributes.
     *
     * @param array $attributes Attributes
     *
     * @api
     */
    public function replace(array $attributes)
    {
        $this->start();

        $_SESSION['_symfony2']['attributes'] = $attributes;
    }

    /**
     * Removes an attribute.
     *
     * @param string $name
     *
     * @api
     */
    public function remove($name)
    {
        $this->start();

        if ($this->has($name)) {
            unset($_SESSION['_symfony2']['attributes'][$name]);
        }
    }

    /**
     * Clears all attributes.
     *
     * @api
     */
    public function clear()
    {
        parent::clear();

        $_SESSION['_symfony2']['attributes'] = array();
        $_SESSION['_symfony2']['flashes'] = array();
    }

    /**
     * Returns the locale
     *
     * @return string
     */
    public function getLocale()
    {
        $this->start();

        return $_SESSION['_symfony2']['locale'];
    }

    /**
     * Sets the locale.
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        parent::setLocale($locale);

        $_SESSION['_symfony2']['locale'] = $locale;
    }

    /**
     * Gets the flash messages.
     *
     * @return array
     */
    public function getFlashes()
    {
        return $_SESSION['_symfony2']['flashes'];
    }

    /**
     * Sets the flash messages.
     *
     * @param array $values
     */
    public function setFlashes($values)
    {
        $this->start();

        $_SESSION['_symfony2']['flashes'] = $values;
        $_SESSION['_symfony2']['oldFlashes'] = array();
    }

    /**
     * Gets a flash message.
     *
     * @param string      $name
     * @param string|null $default
     *
     * @return string
     */
    public function getFlash($name, $default = null)
    {
        return isset($_SESSION['_symfony2']['flashes'][$name]) ? $_SESSION['_symfony2']['flashes'][$name] : $default;
    }

    /**
     * Sets a flash message.
     *
     * @param string $name
     * @param string $value
     */
    public function setFlash($name, $value)
    {
        $this->start();

        $_SESSION['_symfony2']['flashes'][$name] = $value;
        unset($_SESSION['_symfony2']['oldFlashes'][$name]);
    }

    /**
     * Checks whether a flash message exists.
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function hasFlash($name)
    {
        $this->start();

        return isset($_SESSION['_symfony2']['flashes'][$name]);
    }

    /**
     * Removes a flash message.
     *
     * @param string $name
     */
    public function removeFlash($name)
    {
        $this->start();

        unset($_SESSION['_symfony2']['flashes'][$name]);
    }

    /**
     * Removes the flash messages.
     */
    public function clearFlashes()
    {
        $this->start();

        $_SESSION['_symfony2']['flashes'] = array();
        $_SESSION['_symfony2']['oldFlashes'] = array();
    }

    public function serialize()
    {
        throw new \LogicException('It\'s Drupal who serialize or unserialize the data session.');
    }

    public function unserialize($serialized)
    {
        throw new \LogicException('It\'s Drupal who serialize or unserialize the data session.');
    }
}
