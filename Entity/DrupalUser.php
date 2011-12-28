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


abstract class DrupalUser extends \stdClass
{
    const STATUS_BLOCKED = 0;
    const STATUS_ENABLED = 1;

    public $uid;
    public $pass;
    public $name;
    public $mail;
    public $theme;
    public $signature;
    public $signature_format;
    public $created;
    public $access;
    public $login;
    public $status;
    public $timezone;
    public $language;
    public $picture;
    public $init;
    public $data;

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

    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    public function getPass()
    {
        return $this->pass;
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

    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    public function getUid()
    {
        return $this->uid;
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
}
