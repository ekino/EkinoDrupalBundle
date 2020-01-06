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

use Ekino\Bundle\DrupalBundle\Event\DrupalEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * These methods are called by the drupal user hook
 *
 * see for more information about parameters http://api.drupal.org/api/drupal/modules--user--user.api.php/7
 */
class UserRegistrationHookListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var array
     */
    protected $providerKeys;

    /**
     * @param LoggerInterface $logger
     * @param RequestStack    $requestStack
     * @param array           $providerKeys
     */
    public function __construct(LoggerInterface $logger, RequestStack $requestStack, array $providerKeys)
    {
        $this->logger       = $logger;
        $this->requestStack = $requestStack;
        $this->providerKeys = $providerKeys;
    }

    /**
     * http://api.drupal.org/api/drupal/modules--user--user.api.php/function/hook_user_login/7
     *
     * @param DrupalEvent $event
     */
    public function onLogin(DrupalEvent $event)
    {
        $user = $event->getParameter(1);

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('An instance of UserInterface is expected');
        }

        $request = $this->requestStack->getMasterRequest();

        // The ContextListener from the Security component is hijacked to insert a valid token into session
        // so next time the user go to a valid symfony2 url with a proper security context, then the following token
        // will be used
        foreach ($this->providerKeys as $providerKey) {
            $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
            $request->getSession()->set('_security_'.$providerKey, serialize($token));
        }
    }

    /**
     * @param DrupalEvent $event
     */
    public function onLogout(DrupalEvent $event)
    {
        $request = $this->requestStack->getMasterRequest();

        foreach ($this->providerKeys as $providerKey) {
            $request->getSession()->set('_security_'.$providerKey, null);
        }
    }
}
