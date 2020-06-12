<?php

namespace Weble\JoomlaTestBench\Tests;

class JoomlaSetupTest extends \Weble\JoomlaTestBench\TestCase
{
    /** @test */
    public function can_reach_joomla()
    {
        $this->assertTrue($this->get('/index.php?option=com_users&view=login')->successful());
        $this->assertTrue($this->get('/index.php?option=com_users&view=login')->see('Login'));
    }
}
