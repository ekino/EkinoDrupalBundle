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

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

use Ekino\Bundle\DrupalBundle\Drupal\Drupal;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 * @author Florent Denis <fdenis@ekino.com>
 */
class DrupalSessionStorage implements SessionStorageInterface
{
    /**
     * Array of SessionBagInterface
     *
     * @var SessionBagInterface[]
     */
    protected $bags;

    /**
     * @var Boolean
     */
    protected $started = false;

    /**
     * @var Boolean
     */
    protected $closed = false;

    /**
     * @var MetadataBag
     */
    protected $metadataBag;

    /**
     * @var Drupal
     */
    protected $drupal;

    /**
     * @var bool
     */
    protected $refreshCookieLifetime;

    /**
     * Constructor.
     *
     * @param Drupal           $drupal                A Drupal instance
     * @param bool             $refreshCookieLifetime Do we need to refresh session cookie lifetime?
     * @param null|MetadataBag $metaBag               A metadata bag (optional)
     */
    public function __construct(Drupal $drupal, $refreshCookieLifetime, MetadataBag $metaBag = null)
    {
        $this->drupal = $drupal;
        $this->refreshCookieLifetime = $refreshCookieLifetime;

        $this->setMetadataBag($metaBag);
    }

    /**
     * Starts the session.
     *
     * @api
     */
    public function start()
    {
        if ($this->started && !$this->closed) {
            return true;
        }

        $this->drupal->initialize();

        $this->started = drupal_session_started();

        // refresh cookie lifetime if enabled in configuration
        if ($this->started && $this->refreshCookieLifetime) {
            $params = session_get_cookie_params();
            $expire = $params['lifetime'] ? REQUEST_TIME + $params['lifetime'] : 0;

            setcookie(session_name(), session_id(), $expire, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        // force start session
        if (!$this->started) {
            drupal_session_start();

            $this->started = drupal_session_started();
        }

        $this->loadSession();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        if (!$this->started) {
            $this->start();
        }

        return session_id();
    }

    /**
     * {@InheritDoc}
     */
    public function setId($id)
    {
        throw new \LogicException('It\'s Drupal who set session id.');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $this->start();

        return session_name();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        throw new \LogicException('It\'s Drupal who set session name.');
    }

    /**
     * {@InheritDoc}
     */
    public function regenerate($destroy = false, $lifetime = null)
    {
        $this->start();

        drupal_session_regenerate();
    }

    /**
     * {@InheritDoc}
     */
    public function save()
    {
        $this->closed = true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // clear out the bags
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        // clear out the session
        $_SESSION = array();

        // reconnect the bags to the session
        $this->loadSession();
    }

    /**
     * {@InheritDoc}
     */
    public function registerBag(SessionBagInterface $bag)
    {
        $this->bags[$bag->getName()] = $bag;
    }

    /**
     * {@inheritdoc}
     */
    public function getBag($name)
    {
        if (!isset($this->bags[$name])) {
            throw new \InvalidArgumentException(sprintf('The SessionBagInterface %s is not registered.', $name));
        }

        $this->start();

        return $this->bags[$name];
    }

    /**
     * Sets the MetadataBag.
     *
     * @param MetadataBag $metaBag
     */
    public function setMetadataBag(MetadataBag $metaBag = null)
    {
        if (null === $metaBag) {
            $metaBag = new MetadataBag();
        }

        $this->metadataBag = $metaBag;
    }

    /**
     * {@InheritDoc}
     */
    public function getMetadataBag()
    {
        return $this->metadataBag;
    }

    /**
     * {@InheritDoc}
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Sets session.* ini variables.
     *
     * For convenience we omit 'session.' from the beginning of the keys.
     * Explicitly ignores other ini keys.
     *
     * @param array $options Session ini directives array(key => value).
     *
     * @see http://php.net/session.configuration
     */
    public function setOptions(array $options)
    {
        $validOptions = array_flip(array(
            'cache_limiter', 'cookie_domain', 'cookie_httponly',
            'cookie_lifetime', 'cookie_path', 'cookie_secure',
            'entropy_file', 'entropy_length', 'gc_divisor',
            'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
            'hash_function', 'name', 'referer_check',
            'serialize_handler', 'use_cookies',
            'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
            'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
            'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',
        ));

        foreach ($options as $key => $value) {
            if (isset($validOptions[$key])) {
                ini_set('session.'.$key, $value);
            }
        }
    }

    /**
     * Load the session with attributes.
     *
     * After starting the session, PHP retrieves the session from whatever handlers
     * are set to (either PHP's internal, or a custom save handler set with session_set_save_handler()).
     * PHP takes the return value from the read() handler, unserializes it
     * and populates $_SESSION with the result automatically.
     */
    protected function loadSession()
    {
        foreach ($this->bags as $bag) {
            $key = $bag->getStorageKey();
            $_SESSION[$key] = isset($_SESSION[$key]) ? $_SESSION[$key] : array();
            $bag->initialize($_SESSION[$key]);
        }

        $this->started = true;
        $this->closed = false;
    }
}
