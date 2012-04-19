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
 * @author Laurent Kazus <kazus@ekino.com>
 */
class LoggerWatchdog implements LoggerInterface
{

    const LOGGER_EMERGENCY  = 0;
    const LOGGER_ALERT      = 1;
    const LOGGER_CRITICAL   = 2;
    const LOGGER_ERROR      = 3;
    const LOGGER_WARNING    = 4;
    const LOGGER_NOTICE     = 5;
    const LOGGER_INFO       = 6;
    const LOGGER_DEBUG      = 7;

    /**
     * @param $priority
     * @param $message
     * @param array $variables
     */
    protected function log($priority, $message, array $variables = array())
    {
        if (function_exists('watchdog')) {
            watchdog('Symfony2', $message, $variables, $priority);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function emerg($message, array $context = array())
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
    public function crit($message, array $context = array())
    {
        $this->log(self::LOGGER_CRITICAL, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function err($message, array $context = array())
    {
        $this->log(self::LOGGER_ERROR, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warn($message, array $context = array())
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
}