<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Log;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * @author Laurent Kazus <kazus@ekino.Com>
 */
class LoggerWatchdog implements LoggerInterface
{
    /**
     * @var array
     */
    protected $logs;

    public function __construct()
    {
        $this->logs = array();
    }

    /**
     * Persist the log messages
     */
    public function __destruct()
    {
        foreach ($this->logs as $priority => $logs) {
            foreach ($logs as $log) {
                watchdog($log['type'], $log['message'], $log['variables'], $priority);
            }
        }
    }

    /**
     * @param $priority
     * @param $message
     * @param array $variables
     */
    public function log($priority, $message, array $variables = array())
    {
        $this->logs[$priority][] = array(
            'type'      => 'Symfony2',
            'message'   => $message,
            'variables' => $variables
        );
    }

    /**
     * {@inheritdoc}
     */
    public function emerg($message, array $context = array())
    {
        $this->log(WATCHDOG_EMERGENCY, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = array())
    {
        $this->log(WATCHDOG_ALERT, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function crit($message, array $context = array())
    {
        $this->log(WATCHDOG_CRITICAL, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function err($message, array $context = array())
    {
        $this->log(WATCHDOG_ERROR, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warn($message, array $context = array())
    {
        $this->log(WATCHDOG_WARNING, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = array())
    {
        $this->log(WATCHDOG_NOTICE, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = array())
    {
        $this->log(WATCHDOG_INFO, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = array())
    {
        $this->log(WATCHDOG_DEBUG, $message, $context);
    }
}