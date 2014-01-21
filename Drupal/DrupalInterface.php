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
    function initialize();

    /**
     * The shutdown method only catches exit instruction from the Drupal code to rebuild the correct response
     *
     * @param integer $level
     *
     * @return mixed
     */
    function shutdown($level);

    /**
     * Disables the response
     */
    function disableResponse();

    /**
     * Return true if the current Drupal object contains a valid Response object
     *
     * @return boolean
     */
    function hasResponse();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    function is403();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    function is404();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    function isOffline();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    function isOnline();

    /**
     * @return boolean
     *
     * @throws InvalidStateMethodCallException
     */
    function isFound();

    /**
     * Return true if the Drupal is correctly installed
     *
     * @return boolean
     */
    function isInstalled();

    /**
     * This method builds the state of the current Drupal instance
     * @see menu_execute_active_handler function for more information
     *
     * @param Request $request
     */
    function defineState(Request $request);

    /**
     * Decorates the inner content and renders the page
     *
     * @throws InvalidStateMethodCallException
     */
    function render();

    /**
     * Builds the content
     */
    function buildContent();

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function getResponse();

    /**
     * @param integer $pageResultCallback
     */
    function setPageResultCallback($pageResultCallback);

    /**
     * @return integer
     */
    function getPageResultCallback();

    /**
     * @param array $routerItem
     */
    function setRouterItem($routerItem);

    /**
     * @return array
     */
    function getRouterItem();
}
