<?php

namespace Weble\JoomlaTestBench\Concerns;

use JFactory;
use Joomla\CMS\User\User;

trait InteractsWithAuthentication
{
    public function actingAs(User $user): self
    {
        return $this->be($user);
    }

    public function be(User $user): self
    {
        \JFactory::$session->set('user', $user);

        return $this;
    }

    public function assertAuthenticated(): self
    {
        $this->assertTrue(JFactory::getUser()->id > 0, 'The user is logged in');

        return $this;
    }

    public function assertGuest($guard = null): self
    {
        $this->assertFalse(JFactory::getUser()->id > 0, 'The user is not logged in');

        return $this;
    }

    public function assertAuthenticatedAs(User $expected): self
    {
        $user = \JFactory::getUser();
        $this->assertTrue(JFactory::getUser()->id > 0, 'The current user is not authenticated.');

        $this->assertInstanceOf(
            get_class(User::class),
            $user,
            'The currently authenticated user is not a User'
        );

        $this->assertSame(
            $expected->id,
            $user->id,
            'The currently authenticated user is not who was expected'
        );

        return $this;
    }
}
