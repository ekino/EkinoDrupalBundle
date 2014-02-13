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

use FOS\UserBundle\Model\UserManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class controls a Drupal instance to provide helper to render a
 * Drupal response into the Symfony framework
 *
 * The class is statefull
 *
 * @author Thomas Rabaix <thomas.rabaix@ekino.com>
 */
class Drupal implements DrupalInterface
{
    const STATE_FRESH          = 0; // the Drupal instance is not initialized
    const STATE_INIT           = 1; // the Drupal instance has been initialized
    const STATE_STATUS_DEFINED = 2; // the response status is known
    const STATE_INNER_CONTENT  = 3; // Drupal has generated the inner content
    const STATE_RESPONSE       = 4; // Drupal has generated the Response object

    /**
     * @var boolean
     */
    protected $initialized = false;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var integer
     */
    protected $status;

    /**
     * @var integer
     */
    protected $state;

    /**
     * @var array
     */
    protected $routerItem;

    /**
     * @var boolean
     */
    protected $encapsulated;

    /**
     * @var integer
     */
    protected $pageResultCallback;

    /**
     * @var boolean
     */
    protected $disableResponse;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * Constructor
     *
     * @param string               $root        The path of Drupal core
     * @param UserManagerInterface $userManager A user manager instance
     */
    public function __construct($root, UserManagerInterface $userManager)
    {
        $this->root            = $root;
        $this->state           = self::STATE_FRESH;
        $this->response        = new Response;
        $this->encapsulated    = false;
        $this->disableResponse = false;
        $this->userManager     = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;
        $currentLevel = ob_get_level();

        register_shutdown_function(array($this, 'shutdown'), $currentLevel);

        $this->encapsulate(function($path) {
            // start the Drupal bootstrap
            define('DRUPAL_ROOT', $path);

            // make sure the default path point to the correct instance
            chdir($path);

            require_once $path . '/includes/bootstrap.inc';
            drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

            // restore the symfony error handle
            restore_error_handler();
            restore_exception_handler();

        }, $this->root);

        $this->restoreBufferLevel($currentLevel);

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
     * Initializes Drush which boostraps Drupal core
     */
    public function initializeDrush()
    {
        define('DRUSH_BASE_PATH', sprintf('%s/../drush', $this->root));

        define('DRUSH_REQUEST_TIME', microtime(TRUE));

        require_once DRUSH_BASE_PATH . '/includes/bootstrap.inc';
        require_once DRUSH_BASE_PATH . '/includes/environment.inc';
        require_once DRUSH_BASE_PATH . '/includes/command.inc';
        require_once DRUSH_BASE_PATH . '/includes/drush.inc';
        require_once DRUSH_BASE_PATH . '/includes/backend.inc';
        require_once DRUSH_BASE_PATH . '/includes/batch.inc';
        require_once DRUSH_BASE_PATH . '/includes/context.inc';
        require_once DRUSH_BASE_PATH . '/includes/sitealias.inc';
        require_once DRUSH_BASE_PATH . '/includes/exec.inc';
        require_once DRUSH_BASE_PATH . '/includes/drupal.inc';
        require_once DRUSH_BASE_PATH . '/includes/output.inc';
        require_once DRUSH_BASE_PATH . '/includes/cache.inc';
        require_once DRUSH_BASE_PATH . '/includes/filesystem.inc';
        require_once DRUSH_BASE_PATH . '/includes/dbtng.inc';

        $drush_info = drush_read_drush_info();
        define('DRUSH_VERSION', $drush_info['drush_version']);

        $version_parts = explode('.', DRUSH_VERSION);
        define('DRUSH_MAJOR_VERSION', $version_parts[0]);
        define('DRUSH_MINOR_VERSION', $version_parts[1]);

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
     * Fixes the user, Drupal does not provide a hook for anonymous user
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
     * {@inheritdoc}
     */
    public function shutdown($level)
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

        $this->restoreBufferLevel($level);

        $this->response->send();
    }

    /**
     * {@inheritdoc}
     */
    public function disableResponse()
    {
        $this->disableResponse = true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasResponse()
    {
        return !$this->disableResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function is403()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == MENU_ACCESS_DENIED;
    }

    /**
     * {@inheritdoc}
     */
    public function is404()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == MENU_NOT_FOUND;
    }

    /**
     * {@inheritdoc}
     */
    public function isOffline()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == MENU_SITE_OFFLINE;
    }

    /**
     * {@inheritdoc}
     */
    public function isOnline()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status != MENU_SITE_OFFLINE;
    }

    /**
     * {@inheritdoc}
     */
    public function isFound()
    {
        if ($this->state < self::STATE_STATUS_DEFINED) {
            throw new InvalidStateMethodCallException;
        }

        return $this->status == MENU_FOUND;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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

            drupal_deliver_page($pageResultCallback, $defaultDeliveryCallback);
            $drupal->setPageResultCallback($pageResultCallback);
        }, $this);

        $this->response->setContent($content);

        $this->state = self::STATE_RESPONSE;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function setPageResultCallback($pageResultCallback)
    {
        $this->pageResultCallback = $pageResultCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageResultCallback()
    {
        return $this->pageResultCallback;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouterItem($routerItem)
    {
        $this->routerItem = $routerItem;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouterItem()
    {
        return $this->routerItem;
    }

    /**
     * This method executes code related to the Drupal code, and builds a correct response if required
     *
     * @return string
     */
    protected function encapsulate()
    {
        $this->encapsulated = true;
        $args = func_get_args();
        $function = array_shift($args);

        $content = '';

        ob_start(function($buffer) use (&$content) {
            $content .= $buffer;

            return '';
        });

        try {
            call_user_func_array($function, $args);
        } catch (\Exception $e) {
            // @todo: log error message
        }

        ob_end_clean();

        $headers = $this->cleanHeaders();

        foreach ($headers as $name => $value) {
            $this->response->headers->set($name, $value);
        }

        $this->encapsulated = false;

        return $content;
    }

    /**
     * @return array
     */
    protected function cleanHeaders()
    {
        $headers = array();

        foreach (headers_list() as $header) {
            list($name, $value) = explode(':', $header, 2);
            $headers[$name] = trim($value);

            header_remove($name);
        }

        return $headers;
    }

    /**
     * Restores the buffer level by the given one
     *
     * @param integer $level
     */
    protected function restoreBufferLevel($level)
    {
        if (!is_numeric($level)) {
            return;
        }

        while (ob_get_level() > $level) {
            ob_end_flush();
        }
    }
}
