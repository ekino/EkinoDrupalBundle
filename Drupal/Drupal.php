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
use Symfony\Component\HttpFoundation\Response;

use FOS\UserBundle\Model\UserManagerInterface;

/**
 *
 * This class control a drupal instance to provide helper to render a
 * drupal response into the symfony framework
 *
 * The class is statefull
 *
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
final class Drupal implements DrupalInterface
{

    const STATE_FRESH           = 0; // the drupal instance is not initialized
    const STATE_INIT            = 1; // the drupal instance has been initialized
    const STATE_STATUS_DEFINED  = 2; // the response status is known
    const STATE_INNER_CONTENT   = 3; // drupal has generated the inner content
    const STATE_RESPONSE        = 4; // drupal has generated the Response object

    protected $initialized = false;

    protected $root;

    protected $status;

    protected $state;

    protected $routerItem;

    protected $encapsulated;

    protected $pageResultCallback;

    protected $disableResponse;

    protected $userManager;

    /**
     * @param $root
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager
     */
    public function __construct($root, UserManagerInterface $userManager)
    {
        $this->root             = $root;
        $this->state            = self::STATE_FRESH;
        $this->response         = new Response;
        $this->encapsulated     = false;
        $this->disableResponse  = false;
        $this->userManager      = $userManager;
    }

    /**
     * Initialize the Drupal core
     */
    public function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        register_shutdown_function(array($this, 'shutdown'));

        $this->encapsulate(function($path) {
            // start the drupal bootstrap
            define('DRUPAL_ROOT', $path);

            // make sure the default path point to the correct instance
            chdir($path);

            require_once $path . '/includes/bootstrap.inc';
            drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

            // restore the symfony error handle
            restore_error_handler();
            restore_exception_handler();

        }, $this->root);

        $this->fixAnonymousUser();
        $this->state = self::STATE_INIT;
    }

    /**
     * State of initilize.
     *
     * @return boolean 
     */
    public function isInitialized()
    {
        return $this->initialized;
    }

    /**
     * Initialize Drush which boostrap Drupal core
     */
    public function initializeDrush()
    {
        define('DRUSH_BASE_PATH', sprintf('%s/../drush', $this->root));

        define('DRUSH_REQUEST_TIME', microtime(TRUE));

        require_once DRUSH_BASE_PATH . '/includes/environment.inc';
        require_once DRUSH_BASE_PATH . '/includes/command.inc';
        require_once DRUSH_BASE_PATH . '/includes/drush.inc';
        require_once DRUSH_BASE_PATH . '/includes/backend.inc';
        require_once DRUSH_BASE_PATH . '/includes/batch.inc';
        require_once DRUSH_BASE_PATH . '/includes/context.inc';
        require_once DRUSH_BASE_PATH . '/includes/sitealias.inc';

        $GLOBALS['argv'][0] = 'default';

        drush_set_context('arguments', array('default', 'help'));
        drush_set_context('argc', $GLOBALS['argc']);
        drush_set_context('argv', $GLOBALS['argv']);

        drush_set_option('root', $this->root);

        // make sure the default path point to the correct instance
        $currentDirectory = getcwd();
        chdir($this->root);

        $phases = _drush_bootstrap_phases(FALSE, TRUE);
        drush_set_context('DRUSH_BOOTSTRAP_PHASE', DRUSH_BOOTSTRAP_NONE);

        // We need some global options processed at this early stage. Namely --debug.
        _drush_bootstrap_global_options();

        $return = '';
        $command_found = FALSE;

        foreach ($phases as $phase) {
            drush_bootstrap_to_phase($phase);
        }

        chdir($currentDirectory);
    }

    /**
     * Fix the user, drupal does not provide a hook for anonymous user
     *
     * @return
     */
    public function fixAnonymousUser()
    {
        global $user;

        if (!$user || $user->uid != 0) {
            return;
        }

        $user = $this->userManager->createUser()->fromDrupalUser($user);
    }

    /**
     * The shutdown method only catch exit instruction from the drupal code to rebuild the correct response
     *
     * @return mixed
     */
    public function shutdown()
    {
        if (!$this->encapsulated) {
            return;
        }

        $headers = $this->cleanHeaders();

        foreach ($headers as $name => $value) {
            $this->response->headers->set($name, $value);
        }

        $content = ob_get_contents();

        $this->response->setContent($content);

        $this->response->send();
    }

    /**
     * Disable the response
     */
    public function disableResponse()
    {
        $this->disableResponse = true;
    }

    /**
     * Return true if the current drupal object contains a valid Response object
     *
     * @return bool
     */
    public function hasResponse()
    {
        return !$this->disableResponse;
    }

    /**
     * This method execute code related to the drupal code, and build a correct response if required
     *
     * @return string
     */
    private function encapsulate()
    {
        $this->encapsulated = true;
        $args = func_get_args();
        $function = array_shift($args);

        ob_start();
        call_user_func_array($function, $args);
        $content = ob_get_contents();
        ob_clean();

        $headers = $this->cleanHeaders();
        foreach($headers as $name => $value) {
            $this->response->headers->set($name, $value);
        }

        $this->encapsulated = false;

        return $content;
    }

    /**
     * @return array
     */
    private function cleanHeaders()
    {
        $headers = array();
        foreach(headers_list() as $header) {
            list($name, $value) = explode(':', $header, 2);
            $headers[$name] = trim($value);

            header_remove($name);
        }

        return $headers;
    }

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    public function is403()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == MENU_ACCESS_DENIED;
    }


    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    public function is404()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == MENU_NOT_FOUND;
    }

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    public function isOffline()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == MENU_SITE_OFFLINE;
    }

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    public function isOnline()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status != MENU_SITE_OFFLINE;
    }

    /**
     * @return bool
     * @throws InvalidStateMethodCallException
     */
    public function isFound()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == MENU_FOUND;
    }

    /**
     * Return true if the Drupal is correctly installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        if (!$this->response->isRedirect()) {
            return true;
        }

        if (stripos($this->response->headers->get('Location'), 'install.php')) {
            return false;
        }

        return true;
    }

    /**
     * This method build the state of the current drupal instance
     *  see menu_execute_active_handler function for more information
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return void
     */
    public function defineState(Request $request)
    {
        $this->initialize();

        // Check if site is offline.
        $this->status = _menu_site_is_offline() ? MENU_SITE_OFFLINE : MENU_SITE_ONLINE;

        // Allow other modules to change the site status but not the path because that
        // would not change the global variable. hook_url_inbound_alter() can be used
        // to change the path. Code later will not use the $read_only_path variable.
        $read_only_path = $_GET['q'];
        $path = null;

        drupal_alter('menu_site_status', $this->status, $read_only_path);

        $this->state = self::STATE_STATUS_DEFINED;

        $this->pageResultCallback = $this->status;

        // Only continue if the site status is not set.
        if (!$this->isOnline()) {
            return;
        }

        if (!($this->routerItem = menu_get_item($path))) {
            $this->state = self::STATE_INNER_CONTENT;
            $this->pageResultCallback = MENU_NOT_FOUND;
            $this->status = MENU_NOT_FOUND;

            return;
        }

        if (!$this->routerItem['access']) {
            $this->state = self::STATE_INNER_CONTENT;
            $this->status = MENU_ACCESS_DENIED;
            $this->pageResultCallback = MENU_ACCESS_DENIED;

            return;
        }

        $this->status = MENU_FOUND;
    }

    /**
     * Decorate the inner content and render the page
     *
     * @return void
     */
    public function render()
    {
        if ($this->state < self::STATE_INNER_CONTENT) {
            throw new InvalidStateMethodCallException;
        }

        if ($this->state == self::STATE_RESPONSE) {
            return;
        }

        // Deliver the result of the page callback to the browser, or if requested,
        // return it raw, so calling code can do more processing.
        $content = $this->encapsulate(function(DrupalInterface $drupal) {
            $routerItem = $drupal->getRouterItem();
            $defaultDeliveryCallback = $routerItem ? $routerItem['delivery_callback'] : NULL;

            $pageResultCallback = $drupal->getPageResultCallback();
            ob_clean();
            drupal_deliver_page($pageResultCallback, $defaultDeliveryCallback);
            $drupal->setPageResultCallback($pageResultCallback);
            return ob_get_clean();
        }, $this);

        $this->response->setContent($content);

        $this->state = self::STATE_RESPONSE;
    }

    /**
     * Render the content
     */
    public function buildContent()
    {
        if ($this->state > self::STATE_INNER_CONTENT) {
            throw new InvalidStateMethodCallException;
        }

        if ($this->routerItem['include_file']) {
            require_once $this->root . '/' . $this->routerItem['include_file'];
        }

        $this->pageResultCallback = call_user_func_array($this->routerItem['page_callback'], $this->routerItem['page_arguments']);

        $this->state = self::STATE_INNER_CONTENT;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param $pageResultCallback
     */
    public function setPageResultCallback($pageResultCallback)
    {
        $this->pageResultCallback = $pageResultCallback;
    }

    /**
     * @return mixed
     */
    public function getPageResultCallback()
    {
        return $this->pageResultCallback;
    }

    /**
     * @param $routerItem
     */
    public function setRouterItem($routerItem)
    {
        $this->routerItem = $routerItem;
    }

    /**
     * @return mixed
     */
    public function getRouterItem()
    {
        return $this->routerItem;
    }
}
