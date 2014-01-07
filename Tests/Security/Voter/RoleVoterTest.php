<?php

namespace Ekino\Bundle\DrupalBundle\Tests\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

use Ekino\Bundle\DrupalBundle\Security\Voter\RoleVoter;

class RoleVoterTest extends \PHPUnit_Framework_TestCase
{
    public function testSupportsClass()
    {
        $voter = new RoleVoter();

        $this->assertTrue($voter->supportsClass('Foo'));
    }

    /**
     * @dataProvider getVoteTests
     */
    public function testVote($permissions, $attributes, $expected)
    {
        global $user;

        $user->permissions = $permissions;

        $voter = new RoleVoter();

        $this->assertSame($expected, $voter->vote($this->getTokenMock(), null, $attributes));
    }

    public function getVoteTests()
    {
        return array(
            array(array(), array(), VoterInterface::ACCESS_ABSTAIN),
            array(array(), array('FOO'), VoterInterface::ACCESS_ABSTAIN),
            array(array(), array('PERMISSION_DRUPAL_FOO'), VoterInterface::ACCESS_DENIED),
            array(array('foo'), array('PERMISSION_DRUPAL_FOO'), VoterInterface::ACCESS_GRANTED),
            array(array('foo'), array('FOO', 'PERMISSION_DRUPAL_FOO'), VoterInterface::ACCESS_GRANTED),
            array(array('bar', 'foo'), array('PERMISSION_DRUPAL_FOO'), VoterInterface::ACCESS_GRANTED),
        );
    }

    protected function getTokenMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }
}

