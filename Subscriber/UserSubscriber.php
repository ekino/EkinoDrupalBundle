<?php

namespace Ekino\Bundle\DrupalBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 *
 *
 * Original definition:
 *     CREATE TABLE `users` (
 *      `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Primary Key: Unique user ID.',
 *      `name` varchar(60) NOT NULL DEFAULT '' COMMENT 'Unique user name.',
 *      `pass` varchar(128) NOT NULL DEFAULT '' COMMENT 'User’s password (hashed).',
 *      `mail` varchar(254) DEFAULT '' COMMENT 'User’s e-mail address.',
 *      `theme` varchar(255) NOT NULL DEFAULT '' COMMENT 'User’s default theme.',
 *      `signature` varchar(255) NOT NULL DEFAULT '' COMMENT 'User’s signature.',
 *      `signature_format` varchar(255) DEFAULT NULL COMMENT 'The filter_format.format of the signature.',
 *      `created` int(11) NOT NULL DEFAULT '0' COMMENT 'Timestamp for when user was created.',
 *      `access` int(11) NOT NULL DEFAULT '0' COMMENT 'Timestamp for previous time user accessed the site.',
 *      `login` int(11) NOT NULL DEFAULT '0' COMMENT 'Timestamp for user’s last login.',
 *      `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Whether the user is active(1) or blocked(0).',
 *      `timezone` varchar(32) DEFAULT NULL COMMENT 'User’s time zone.',
 *      `language` varchar(12) NOT NULL DEFAULT '' COMMENT 'User’s default language.',
 *      `picture` int(11) NOT NULL DEFAULT '0' COMMENT 'Foreign key: file_managed.fid of user’s picture.',
 *      `init` varchar(254) DEFAULT '' COMMENT 'E-mail address used for initial account creation.',
 *      `data` longblob COMMENT 'A serialized array of name value pairs that are related to the user. Any form values posted during user edit are stored and are loaded into the $user object during user_load(). Use of this field is discouraged and it will likely disappear in a future...',
 *      PRIMARY KEY (`uid`),
 *      UNIQUE KEY `name` (`name`),
 *      KEY `access` (`access`),
 *      KEY `created` (`created`),
 *      KEY `mail` (`mail`)
 *    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores user data.';
 *
 */
class UserSubscriber implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata'
        );
    }

    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs
     * @return mixed
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->name !== 'Ekino\Bundle\DrupalBundle\Entity\User') {
            return;
        }

        // customize field
        $metadata->fieldMappings['username']['columnName'] = 'name';
        $metadata->fieldMappings['username']['type']       = 'string';

        $metadata->fieldMappings['email']['columnName'] = 'mail';
        $metadata->fieldMappings['email']['type']       = 'string';

        $metadata->fieldMappings['password']['columnName'] = 'pass';
        $metadata->fieldMappings['password']['type']       = 'string';
        $metadata->fieldMappings['password']['length']     = 128;

        $metadata->fieldMappings['username']['columnName'] = 'name';
        $metadata->fieldMappings['username']['type']       = 'string';

        $metadata->fieldMappings['id']['columnDefinition'] = 'int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'Primary Key: Unique user ID.\'';


        // add custome indexes
        $metadata->table['indexes']['name'] = array('columns' => array('name'));

        /**
         * algorithm => drupal
         * username , usernameCanonical => name
         * email, emailCanonical => email
         * enabled => $array['status'] == 1 ? 'status_activated' : 'status_blocked';
         * ?? => created
         * ?? => access
         */

    }
}
