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

use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;

abstract class FosUser extends DrupalUser implements SecurityUserInterface, FOSUserInterface
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $usernameCanonical;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $emailCanonical;

    /**
     * @var boolean
     */
    public $enabled;

    /**
     * The algorithm to use for hashing
     *
     * @var string
     */
    public $algorithm;

    /**
     * The salt to use for hashing
     *
     * @var string
     */
    public $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    public $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    public $plainPassword;

    /**
     * @var \DateTime
     */
    public $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     */
    public $confirmationToken;

    /**
     * @var \DateTime
     */
    public $passwordRequestedAt;

    /**
     * @var Collection
     */
    public $groups;

    /**
     * @var Boolean
     */
    public $locked;

    /**
     * @var Boolean
     */
    public $expired;

    /**
     * @var \DateTime
     */
    public $expiresAt;

    /**
     * @var array
     */
    public $roles;

    /**
     * @var Boolean
     */
    public $credentialsExpired;

    /**
     * @var \DateTime
     */
    public $credentialsExpireAt;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->generateConfirmationToken();
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->roles = array();
        $this->credentialsExpired = false;
    }

    /**
     * Adds a role to the user.
     *
     * @param string $role
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    /**
     * Implementation of SecurityUserInterface.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return Boolean
     */
    public function equals(SecurityUserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }
        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }
        if ($this->usernameCanonical !== $user->getUsernameCanonical()) {
            return false;
        }
        if ($this->isAccountNonExpired() !== $user->isAccountNonExpired()) {
            return false;
        }
        if (!$this->locked !== $user->isAccountNonLocked()) {
            return false;
        }
        if ($this->isCredentialsNonExpired() !== $user->isCredentialsNonExpired()) {
            return false;
        }
        if ($this->enabled !== $user->isEnabled()) {
            return false;
        }

        return true;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled
        ) = unserialize($serialized);
    }

    /**
     * Removes sensitive data from the user.
     *
     * Implements SecurityUserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gets the canonical username in search and sort queries.
     *
     * @return string
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * Implementation of SecurityUserInterface
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Gets the algorithm used to encode the password.
     *
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Gets the canonical email in search and sort queries.
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Gets the encrypted password.
     *
     * Implements SecurityUserInterface
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Gets the confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Returns the user roles
     *
     * Implements SecurityUserInterface
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

//        foreach ($this->getGroups() as $group) {
//            $roles = array_merge($roles, $group->getRoles());
//        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
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
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Implements AdvancedUserInterface
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     */
    public function isAccountNonExpired()
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Implements AdvancedUserInterface
     *
     * @return Boolean true if the user is not locked, false otherwise
     */
    public function isAccountNonLocked()
    {
        return !$this->locked;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Implements AdvancedUserInterface
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     */
    public function isCredentialsNonExpired()
    {
        if (true === $this->credentialsExpired) {
            return false;
        }

        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    public function isCredentialsExpired()
    {
        return !$this->isCredentialsNonExpired();
    }

    /**
     * Checks whether the user is enabled.
     *
     * Implements AdvancedUserInterface
     *
     * @return Boolean true if the user is enabled, false otherwise
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    public function isExpired()
    {
        return !$this->isAccountNonExpired();
    }

    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return Boolean
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
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
        return null !== $user && $this->getId() === $user->getId();
    }

    /**
     * Removes a role to the user.
     *
     * @param string $role
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    /**
     * Sets the username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Sets the canonical username.
     *
     * @param string $usernameCanonical
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;
    }

    /**
     * Sets the algorithm
     *
     * @param string $algorithm
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    public function setCredentialsExpireAt(\DateTime $date)
    {
        $this->credentialsExpireAt = $date;
    }

    public function setCredentialsExpired($boolean)
    {
        $this->credentialsExpired = $boolean;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Set the canonical email.
     *
     * @param string $emailCanonical
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
    }

    /**
     * @param Boolean $boolean
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (Boolean) $boolean;
    }

    /**
     * Sets this user to expired.
     *
     * @param Boolean $boolean
     */
    public function setExpired($boolean)
    {
        $this->expired = (Boolean) $boolean;
    }

    public function setExpiresAt(\DateTime $date)
    {
        $this->expiresAt = $date;
    }

    /**
     * Sets the hashed password.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Sets the super admin status
     *
     * @param Boolean $boolean
     */
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
    }

    /**
     * Sets the plain password.
     *
     * @param string $password
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * Sets the last login time
     *
     * @param \DateTime $time
     */
    public function setLastLogin(\DateTime $time)
    {
        $this->lastLogin = $time;
    }

    /**
     * Sets the locking status of the user.
     *
     * @param Boolean $boolean
     */
    public function setLocked($boolean)
    {
        $this->locked = $boolean;
    }

    /**
     * Sets the confirmation token
     *
     * @param string $confirmationToken
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * Sets the timestamp that the user requested a password reset.
     *
     * @param \DateTime $date
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Checks whether the password reset request has expired.
     *
     * @param integer $ttl Requests older than this many seconds will be considered expired
     * @return Boolean true if the user's password request is non expired, false otherwise
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->passwordRequestedAt instanceof \DateTime &&
               $this->passwordRequestedAt->getTimestamp() + $ttl > time();
    }

    /**
     * Generates the confirmation token if it is not set.
     */
    public function generateConfirmationToken()
    {
        if (null === $this->confirmationToken) {
            $this->confirmationToken = $this->generateToken();
        }
    }

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * Generates a token.
     *
     * @return string
     */
    protected function generateToken()
    {
        $bytes = false;
        if (function_exists('openssl_random_pseudo_bytes') && 0 !== stripos(PHP_OS, 'win')) {
            $bytes = openssl_random_pseudo_bytes(32, $strong);

            if (true !== $strong) {
                $bytes = false;
            }
        }

        // let's just hope we got a good seed
        if (false === $bytes) {
            $bytes = hash('sha256', uniqid(mt_rand(), true), true);
        }

        return base_convert(bin2hex($bytes), 16, 36);
    }
}
