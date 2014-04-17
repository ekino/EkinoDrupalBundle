<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {
    $started = false;
    $time = 0;
}

/**
 * Mock Drupal function calls
 */
namespace Ekino\Bundle\DrupalBundle\Port {
    /**
     * Mock call to Drupal drupal_session_started() function
     *
     * @return bool
     */
    function drupal_session_started()
    {
        global $started;

        return $started;
    }

    /**
     * Mock call to Drupal drupal_session_start() function
     *
     * @return void
     */
    function drupal_session_start()
    {
        global $time;

        $time += 1;
    }

    /**
     * Mock PHP built-in setcookie() function
     *
     * @param $name
     * @param $value
     * @param $expire
     * @param $path
     * @param $domain
     * @param $secure
     * @param $httponly
     */
    function setcookie($name, $value, $expire, $path, $domain, $secure, $httponly)
    {
        global $time;

        $time += 1;
    }
}

namespace Ekino\Bundle\DrupalBundle\Tests\Port {

    use Ekino\Bundle\DrupalBundle\Port\DrupalSessionStorage;

    /**
     * Tests the Drupal session storage
     *
     * @author Vincent Composieux <vincent.composieux@gmail.com>
     */
    class DrupalSessionStorageTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Tests Drupal session storage service
         */
        public function testSessionStorage()
        {
            // Given
            global $time;

            $session = $this->getDrupalSessionStorageMock(false);

            // When
            $session->start();

            // Then
            $this->assertEquals(1, $time, 'Time should be equal to 1 because 1 iteration is done');
        }

        /**
         * Tests Drupal session storage service with cookie session refresh lifetime
         */
        public function testSessionStorageWithRefreshLifetime()
        {
            // Given
            global $started, $time;

            $session = $this->getDrupalSessionStorageMock(true);

            $started = true;

            // When
            $session->start();

            // Then
            $this->assertEquals(2, $time, 'Time should be equal to 2 because 2 iteration is done');
        }

        /**
         * Returns DrupalSessionStorage mock
         *
         * @param bool $refreshCookieLifetime
         *
         * @return DrupalSessionStorage
         */
        protected function getDrupalSessionStorageMock($refreshCookieLifetime)
        {
            $drupal = $this->getMockBuilder('Ekino\Bundle\DrupalBundle\Drupal\Drupal')
                ->disableOriginalConstructor()
                ->getMock();

            $bag = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionBagInterface');
            $bag->expects($this->any())->method('getName')->will($this->returnValue('test'));

            $session = new DrupalSessionStorage($drupal, $refreshCookieLifetime);
            $session->registerBag($bag);

            return $session;
        }
    }
}