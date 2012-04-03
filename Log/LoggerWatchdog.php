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
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * @author Laurent Kazus <kazus@ekino.com>
 */
class LoggerWatchdog implements LoggerInterface, DebugLoggerInterface
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
     * @var array
     */
    protected $logs;

    public function __construct()
    {
        $this->logs = array(
            self::LOGGER_EMERGENCY  => array(),
            self::LOGGER_ALERT      => array(),
            self::LOGGER_CRITICAL   => array(),
            self::LOGGER_ERROR      => array(),
            self::LOGGER_WARNING    => array(),
            self::LOGGER_NOTICE     => array(),
            self::LOGGER_INFO       => array(),
            self::LOGGER_DEBUG      => array()
        );
    }

    /**
     * Persist the log messages
     */
    public function __destruct()
    {
        foreach ($this->logs as $priority => $logs) {
            foreach ($logs as $log) {
                watchdog($log['context'], $log['message'], $log['variables'], $priority);
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
            'context'      => 'Symfony2',
            'message'      => $message,
            'priorityName' => $this->getPriorityName($priority),
            'variables'    => $variables
        );
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

    /**
     * Returns an array of logs.
     *
     * A log is an array with the following mandatory keys:
     * timestamp, message, priority, and priorityName.
     * It can also have an optional context key containing an array.
     *
     * @return array An array of logs
     */
    public function getLogs()
    {
        $logs = array();

        foreach ($this->logs as $logsByPriority) {
            foreach ($logsByPriority as $log) {
                $logs[] = $log;
            }
        }

        return $logs;
    }

    /**
     * Returns the number of errors.
     *
     * @return integer The number of errors
     */
    public function countErrors()
    {
        return count($this->logs[self::LOGGER_ERROR]) + count($this->logs[self::LOGGER_CRITICAL]);
    }

    /**
     * Get the priority name
     *
     * @param  integer $priority The priority id
     *
     * @return string The priority name
     */
    private function getPriorityName($priority)
    {
        return ($priority != self::LOGGER_INFO && $priority != self::LOGGER_DEBUG) ? 'ERROR' : 'INFO';
    }
}