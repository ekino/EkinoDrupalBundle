<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Event\Listener;

/**
 * Tests UserRegistrationHookListener
 *
 * @author Louis Courvoisier <louis.courvoisier@ekino.com>
 */
class UserRegistrationHookListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Asserts the onLogin method throws an exception if user not instance of UserInterface
     *
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage An instance of UserInterface is expected
     */
    public function testOnLoginThrowsException()
    {
        $logger   = $this->getMock('Psr\Log\LoggerInterface');
        $requestStack  = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
        $listener = new UserRegistrationHookListener($logger, $requestStack, array());

        $event = $this->getMock('Ekino\Bundle\DrupalBundle\Event\DrupalEvent');
        $event->expects($this->once())->method('getParameter')->with($this->equalTo(1))->willReturn(null);

        $listener->onLogin($event);
    }

    /**
     * Test onLogin method with provider keys
     */
    public function testOnLoginUserWithProviderKeys()
    {
        $logger  = $this->getMock('Psr\Log\LoggerInterface');
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($this->exactly(3))->method('getSession')->willReturn($session);

        $requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->once())->method('getMasterRequest')->willReturn($request);

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())->method('getRoles')->willReturn(array('ROLE_USER'));

        $event = $this->getMock('Ekino\Bundle\DrupalBundle\Event\DrupalEvent');
        $event->expects($this->once())->method('getParameter')->with($this->equalTo(1))->willReturn($user);

        $listener = new UserRegistrationHookListener($logger, $requestStack, array('1', '2', '3'));
        $listener->onLogin($event);
    }

    /**
     * Test onLogin method with no provider keys
     */
    public function testOnLoginUserWithoutProviderKeys()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($this->never())->method('getSession');

        $requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->once())->method('getMasterRequest')->willReturn($request);

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $event = $this->getMock('Ekino\Bundle\DrupalBundle\Event\DrupalEvent');
        $event->expects($this->once())->method('getParameter')->with($this->equalTo(1))->willReturn($user);

        $listener = new UserRegistrationHookListener($logger, $requestStack, array());
        $listener->onLogin($event);
    }
}
