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

    protected function getSiteRoot(): string
    {
        return realpath(__DIR__ . '/../vendor/joomla/joomla-cms');
    }

    protected function getDbFiles(): array
    {
        $dir   = realpath(__DIR__ . '/../../vendor/joomla/joomla-cms/installation/sql/mysql');
        return  [$dir . ' /joomla.sql', $dir . '/sample_testing.sql'];
    }


}
