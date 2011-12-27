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

use Sonata\UserBundle\Entity\BaseUser;

class User extends BaseUser
{
    protected $theme;
    protected $signature;
    protected $signatureFormat;
    protected $created;
    protected $access;
    protected $login;
    protected $status;
    protected $timezone;
    protected $language;
    protected $picture;
    protected $init;
    protected $data;

    public function getEmailCanonical()
    {
        return $this->getEmail();
    }

    public function getUsernameCanonical()
    {
        return $this->getUsername();
    }

    public function setEmail($email)
    {
        $this->setEmailCanonical($email);
    }

    public function setEmailCanonical($email)
    {
        $this->email = $email;
        $this->emailCanonical = $email;
    }

    public function setUsername($username)
    {
        $this->setUsernameCanonical($username);
    }

    public function setUsernameCanonical($username)
    {
        $this->username = $username;
        $this->usernameCanonical = $username;
    }

    public function setAccess($access)
    {
        $this->access = $access;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setInit($init)
    {
        $this->init = $init;
    }

    public function getInit()
    {
        return $this->init;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setSignatureFormat($signatureFormat)
    {
        $this->signatureFormat = $signatureFormat;
    }

    public function getSignatureFormat()
    {
        return $this->signatureFormat;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }
}
