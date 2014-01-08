<?php

namespace Ekino\Bundle\DrupalBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * RoleVoter votes if drupal permission attribute.
 *
 * @author Florent Denis <fdenis@ekino.com>
 */
class RoleVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return 0 === strpos($attribute, 'PERMISSION_DRUPAL_');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $user = self::retrieveCurrentUser();

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            if (user_access(self::camelize($attribute), $user)) {
                return VoterInterface::ACCESS_GRANTED;
            }

            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

    /**
     * Camelizes a string.
     *
     * @param string $id A string to camelize for drupal
     *
     * @return string The camelized string
     */
    public static function camelize($id)
    {
        return strtolower(str_replace('_', ' ', substr($id, 18)));
    }

    /**
     * Returns current drupal user.
     *
     * @return \stdClass
     */
    protected static function retrieveCurrentUser()
    {
        global $user;

        return $user;
    }
}
