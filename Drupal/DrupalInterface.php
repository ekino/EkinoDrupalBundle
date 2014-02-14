<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Drupal;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
interface DrupalInterface
{
    /**
     * Initializes the Drupal core
     */
    public function initialize();

    /**
     * The shutdown method only catches exit instruction from the Drupal code to rebuild the correct response
     *
     * @param integer $level
     *
     * @return mixed
     */
    public function shutdown($level);

    /**
     * Disables the response
     */
    public function disableResponse();

    /**
     * Return true if the current Drupal object contains a valid Response object
     *
     * @return boolean
     */
    public function hasResponse();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    public function is403();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    public function is404();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    public function isOffline();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    public function isOnline();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    public function isFound();

    /**
     * Return true if the Drupal is correctly installed
     *
     * @return boolean
     */
    public function isInstalled();

    /**
     * This method builds the state of the current Drupal instance
     * @see menu_execute_active_handler function for more information
     *
     * @param Request $request
     */
    public function defineState(Request $request);

    /**
     * Decorates the inner content and renders the page
     *
     * @throws InvalidStateMethodCallException
     */
    public function render();

    /**
     * Builds the content
     */
    public function buildContent();

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse();

    /**
     * @param integer $pageResultCallback
     */
    public function setPageResultCallback($pageResultCallback);

    /**
     * @return integer
     */
    public function getPageResultCallback();

    /**
     * @param array $routerItem
     */
    public function setRouterItem($routerItem);

    /**
     * @return array
     */
    public function getRouterItem();

    /**
     * Gets info of entity type if given, all entities otherwise
     *
     * @param string $entityType An entity type (like node)
     *
     * @return array
     */
    public function getEntityInfo($entityType = null);

    /**
     * Gets the entity controller
     *
     * @param string $entityType An entity type (like node)
     *
     * @return \DrupalDefaultEntityController
     */
    public function getEntityController($entityType);
}
