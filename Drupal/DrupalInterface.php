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
     * @abstract
     * initialize the Drupal instance
     */
    function initialize();

    /**
     * The shutdown method only catch exit instruction from the drupal code to rebuild the correct response
     *
     * @abstract
     * @return mixed
     */
    function shutdown();

    /**
     * Disable the response
     * @abstract
     */
    function disableResponse();

    /**
     * Return true if the current drupal object contains a valid Response object
     * @abstract
     * @return bool
     */
    function hasResponse();

    /**
     * @abstract
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    function is403();

    /**
     * @abstract
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    function is404();

    /**
     * @abstract
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    function isOffline();

    /**
     * @abstract
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    function isOnline();

    /**
     * @abstract
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    function isFound();

    /**
     * Return true if the Drupal is correctly installed
     *
     * @abstract
     * @return bool
     */
    function isInstalled();

    /**
     * This method build the state of the current drupal instance
     *  see menu_execute_active_handler function for more information
     *
     * @abstract
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return void
     */
    function defineState(Request $request);

    /**
     * Decorate the inner content and render the page
     *
     * @abstract
     * @return void
     */
    function render();

    /**
     * Render the content
     *
     * @abstract
     */
    function buildContent();

    /**
     * @abstract
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function getResponse();

    /**
     * @abstract
     * @param $pageResultCallback
     */
    function setPageResultCallback($pageResultCallback);

    /**
     * @abstract
     * @return mixed
     */
    function getPageResultCallback();

    /**
     * @abstract
     * @param $routerItem
     */
    function setRouterItem($routerItem);

    /**
     * @abstract
     * @return mixed
     */
    function getRouterItem();
}