<?php

$user = new stdClass();
$user->permissions = array();

/**
 * Drupal user_access Mock
 *
 * @param string $string
 * @param mixed  $user
 *
 * @return bool
 */
function user_access($string, $user)
{
    global $user;

    foreach ($user->permissions as $permission) {
        if ($string === $permission) {
            return true;
        }
    }

    return false;
}
