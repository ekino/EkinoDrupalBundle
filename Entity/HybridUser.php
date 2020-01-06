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

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;

/**
 * This class provide convenient proxies methods between FosUser class and a DrupalUser class.
 */
abstract class HybridUser extends FosUser
{

    public function getId()
    {
        return $this->getUid();
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->pass;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getName();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (method_exists($this, 'get'.$name)) {
            return call_user_func(array($this, 'get'.$name));
        }

        return $this->$name;
    }

    /**
     * @param \stdClass $user
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function fromDrupalUser(\stdClass $user)
    {
        foreach ($user as $property => $value) {
            $this->$property = $value;
        }

        return $this;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     */
    public function isAccountNonExpired()
    {
        return $this->status == 1;
    }

    /**
     * Checks whether the user is locked.
     *
     * @return Boolean true if the user is not locked, false otherwise
     */
    public function isAccountNonLocked()
    {
        return $this->status != self::STATUS_BLOCKED;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     */
    public function isCredentialsNonExpired()
    {
        return $this->status != self::STATUS_BLOCKED;
    }

    /**
     * Checks whether the user is enabled.
     *
     * @return Boolean true if the user is enabled, false otherwise
     */
    public function isEnabled()
    {
        return $this->status === self::STATUS_ENABLED;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    public function serialize()
    {
        $values = array();

        foreach ($this as $name => $value) {
            $values[$name] = $value;
        }

        return serialize($values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized)
    {
        $values = unserialize($serialized);

        foreach ($values as $name => $value) {
            $this->$name = $value;
        }

        return $values;
    }

    /**
     * Gets the algorithm used to encode the password.
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return 'drupal';
    }

    /**
     * Sets the username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->name = $username;
    }

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical()
    {
        return $this->name;
    }

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->setName($usernameCanonical);
    }

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getMail();
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->mail = $email;
    }

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->getMail();
    }

    /**
     * Set the canonical email.
     *
     * @param string $emailCanonical
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->setEmail($emailCanonical);
    }

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return null;
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     * @return Boolean
     */
    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * Adds a role to the user.
     *
     * @param string $role
     */
    public function addRole($role)
    {
        if ($role === static::ROLE_DEFAULT) {
            return;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    /**
     * Tells if the the given user is this user.
     *
     * Useful when not hydrating all fields.
     *
     * @param UserInterface $user
     * @return Boolean
     */
    public function isUser(FOSUserInterface $user = null)
    {
        return $user instanceof self && $user->getId() == $this->getId();
    }

    /**
     * Removes a role to the user.
     *
     * @param string $role
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search($role, $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }
}
