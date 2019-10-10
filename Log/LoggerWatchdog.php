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

use Psr\Log\LoggerInterface;

/**
 * LoggerWatchdog.
 *
 * @author Laurent Kazus <kazus@ekino.com>
 * @author Florent Denis <fdenis@ekino.com>
 */
class LoggerWatchdog implements LoggerInterface
{
    const LOGGER_EMERGENCY  = 0; // WATCHDOG_EMERGENCY
    const LOGGER_ALERT      = 1; // WATCHDOG_ALERT
    const LOGGER_CRITICAL   = 2; // WATCHDOG_CRITICAL
    const LOGGER_ERROR      = 3; // WATCHDOG_ERROR
    const LOGGER_WARNING    = 4; // WATCHDOG_WARNING
    const LOGGER_NOTICE     = 5; // WATCHDOG_NOTICE
    const LOGGER_INFO       = 6; // WATCHDOG_INFO
    const LOGGER_DEBUG      = 7; // WATCHDOG_DEBUG

    /**
     * {@inheritdoc}
     */
    public function emerg($message, array $context = array())
    {
        $this->emergency($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function crit($message, array $context = array())
    {
        $this->critical($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function err($message, array $context = array())
    {
        $this->error($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warn($message, array $context = array())
    {
        $this->warning($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = array())
    {
        $this->log(self::LOGGER_EMERGENCY, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = array())
    {
        $this->log(self::LOGGER_ALERT, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = array())
    {
        $this->log(self::LOGGER_CRITICAL, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = array())
    {
        $this->log(self::LOGGER_ERROR, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = array())
    {
        $this->log(self::LOGGER_WARNING, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = array())
    {
        $this->log(self::LOGGER_NOTICE, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = array())
    {
        $this->log(self::LOGGER_INFO, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = array())
    {
        $this->log(self::LOGGER_DEBUG, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        if (function_exists('watchdog')) {
            watchdog('Symfony2', $message, $context, $level);
        }
    }
}
