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

use Ekino\Bundle\DrupalBundle\Event\DrupalEntityEvent;
use Ekino\Bundle\DrupalBundle\Event\DrupalEntitiesEvent;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * These methods are called by the drupal user hook
 *
 * see for more information about parameters http://api.drupal.org/api/drupal/modules--user--user.api.php/7
 */
class UserEntityHookListener
{
    protected $userManager;

    protected $logger;

    /**
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    public function __construct(UserManagerInterface $userManager, LoggerInterface $logger)
    {
        $this->userManager = $userManager;
        $this->logger      = $logger;
    }

    /**
     * http://api.drupal.org/api/drupal/modules--user--user.api.php/function/hook_user_load/7
     *
     * @param \Ekino\Bundle\DrupalBundle\Event\DrupalEvent $event
     * @return void
     */
    public function onLoad(DrupalEntitiesEvent $event)
    {
        if (!$event->isType('user')) {
            return;
        }

        $users =& $event->getEntities();

        foreach ($users as $pos => $drupalUser) {
            $users[$pos] = $this->getSymfonyUser($drupalUser);
        }
    }

    /**
     * @param \stdClass $drupalUser
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function getSymfonyUser(\stdClass $drupalUser)
    {
        $user = $this->userManager->createUser();

        $user->fromDrupalUser($drupalUser);

        return $user;
    }

    /**
     * http://api.drupal.org/api/drupal/modules--user--user.api.php/function/hook_user_insert/7
     *
     * @param \Ekino\Bundle\DrupalBundle\Event\DrupalEvent $event
     * @return void
     */
    public function onInsert(DrupalEntityEvent $event)
    {

    }

    /**
     * @param \Ekino\Bundle\DrupalBundle\Event\DrupalEvent $event
     * @return void
     */
    public function onUpdate(DrupalEntityEvent $event)
    {

    }

    /**
     * http://api.drupal.org/api/drupal/modules--user--user.api.php/function/hook_user_presave/7
     *
     * @param \Ekino\Bundle\DrupalBundle\Event\DrupalEvent $event
     * @return void
     */
    public function onPreSave(DrupalEntityEvent $event)
    {
        if (!$event->isType('user')) {
            return;
        }

        $account = $event->getEntity();

        $account->emailCanonical    = $account->email;
        $account->usernameCanonical = $account->name;
    }
}