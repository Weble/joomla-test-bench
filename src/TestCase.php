<?php

namespace Weble\JoomlaTestBench;

use Weble\JoomlaTestBench\Concerns\CreatesApplication;
use Weble\JoomlaTestBench\Concerns\DatabaseTransactions;
use Weble\JoomlaTestBench\Concerns\DbTestTrait;
use Weble\JoomlaTestBench\Concerns\InteractsWithAuthentication;
use Weble\JoomlaTestBench\Concerns\MakesHttpRequests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use CreatesApplication,
        InteractsWithAuthentication,
        MakesHttpRequests;

    /** @var bool */
    protected $setUpHasRun = false;

    protected function setUp(): void
    {
        parent::setUp();

        // DBTestHelper::setupTest($this);

        $this->setUpTestEnvironment();

        $this->get('/');
    }

    protected function getSiteRoot(): string
    {
        return realpath(__DIR__ . '/../vendor/joomla/joomla-cms');
    }

    protected function getConfigurationFile(): string
    {
        if (file_exists($this->getSiteRoot() . '/configuration.php')) {
            return $this->getSiteRoot() . '/configuration.php';
        }

        return $this->getSiteRoot() . '/installation/configuration.php-dist';
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[DatabaseTransactions::class])) {
            $this->beginDatabaseTransaction();
        }

        return $uses;
    }

    protected function setUpTestEnvironment()
    {
        if (! $this->application) {
            $this->createApplication();
        }

        $this->setUpTraits();

        foreach ($this->afterApplicationCreatedCallbacks as $callback) {
            \call_user_func($callback);
        }

        $this->setUpHasRun = true;
    }
}

if (! function_exists('class_uses_recursive')) {
    /**
     * Returns all traits used by a class, its parent classes and trait of their traits.
     *
     * @param object|string $class
     * @return array
     */
    function class_uses_recursive($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_reverse(class_parents($class)) + [$class => $class] as $class) {
            $results += trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}

if (! function_exists('trait_uses_recursive')) {
    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param string $trait
     * @return array
     */
    function trait_uses_recursive($trait)
    {
        $traits = class_uses($trait);

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}
