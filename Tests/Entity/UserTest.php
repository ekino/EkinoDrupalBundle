<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Tests\Entity;

use \Ekino\Bundle\DrupalBundle\Entity\User;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    public function getDrupalUser()
    {
        $user = new \stdClass();
        $user->mail = 'foo@bar.com';
        $user->name = 'foobar';
        $user->uid = 42;

        return $user;
    }

    public function testFromDrupalUser()
    {
        $drupalUser = $this->getDrupalUser();
        $drupalUser->status = 2;

        $user = new User;
        $user->fromDrupalUser($drupalUser);

        $this->assertEquals('foo@bar.com', $user->getMail());
        //        $this->assertEquals(true, $user->isLocked());
        $this->assertEquals('foobar', $user->getUsername());
    }

    public function testSetterGetter()
    {
        $object = new \stdClass();
        $object->name = 'haha';

        $drupalUser = $this->getDrupalUser();
        $drupalUser->randomProperty = 'foobar';

        $drupalUser->randomObject = $object;
        $drupalUser->name = 'Thomas';


        $user = new User;
        $user->fromDrupalUser($drupalUser);

        $user->foo = array();
        $user->foo += array('salut');

        $this->assertEquals(array('salut'), $user->foo);

        $user->foo['bar'] = 'foobar';

        $this->assertEquals('Thomas', $user->name);
        $this->assertEquals('foo@bar.com', $user->mail);

        $this->assertEquals('foobar', $user->randomProperty);

        $object->name = 'reference';

        $this->assertEquals('reference', $user->randomObject->name);
    }

    public function testSerialize()
    {
        $drupalUser = $this->getDrupalUser();

        $user = new User;
        $user->fromDrupalUser($drupalUser);
        $user->salt = 'salt';
        $user->setConfirmationToken('token');

        $expected = array(
            'username' => NULL,
            'usernameCanonical' => NULL,
            'email' => NULL,
            'emailCanonical' => NULL,
            'enabled' => false,
            'algorithm' => NULL,
            'salt' => 'salt',
            'password' => NULL,
            'plainPassword' => NULL,
            'lastLogin' => NULL,
            'confirmationToken' => 'token',
            'passwordRequestedAt' => NULL,
            'groups' => NULL,
            'locked' => false,
            'expired' => false,
            'expiresAt' => NULL,
            'roles' => array (),
            'credentialsExpired' => false,
            'credentialsExpireAt' => NULL,
            'uid' => 42,
            'pass' => NULL,
            'name' => 'foobar',
            'mail' => 'foo@bar.com',
            'theme' => NULL,
            'signature' => NULL,
            'signature_format' => NULL,
            'created' => NULL,
            'access' => NULL,
            'login' => NULL,
            'status' => NULL,
            'timezone' => NULL,
            'language' => NULL,
            'picture' => NULL,
            'init' => NULL,
            'data' => NULL,
            'path' => NULL
        );

        $this->assertEquals($expected, unserialize($user->serialize()));
    }

    public function testUnserialize()
    {
        $s = serialize($expected = array(
            'uid' => 42,
            'pass' => NULL,
            'name' => 'foobar',
            'mail' => 'foo@bar.com',
            'theme' => NULL,
            'signature' => NULL,
            'signature_format' => NULL,
            'created' => NULL,
            'access' => NULL,
            'login' => NULL,
            'status' => NULL,
            'timezone' => NULL,
            'language' => NULL,
            'picture' => NULL,
            'init' => NULL,
            'data' => NULL,
            'roles' => NULL,
        ));

        $user = new User;

        $values = $user->unserialize($s);

        $this->assertEquals($expected, $values);
        $this->assertEquals(42, $user->getUid());
    }
}