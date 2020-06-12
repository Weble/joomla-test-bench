<?php

namespace Weble\JoomlaTestBench\Concerns;

use JDatabaseFactory;
use JFactory;
use Joomla\CMS\Application\CMSApplication;
use ReflectionClass;
use Weble\JoomlaTestBench\Application\TestApplication;
use Weble\JoomlaTestBench\Input\TestInput;

trait CreatesApplication
{
    /** @var TestApplication */
    protected $application;

    /** @var callable[] */
    protected $afterApplicationCreatedCallbacks = [];

    public function createApplication()
    {
        if ($this->application) {
            return $this->application;
        }

        if (! defined('_JEXEC')) {
            define('_JEXEC', 1);
        }

        if (! defined('JDEBUG')) {
            define('JDEBUG', 0);
        }

        if (! defined('JPATH_TESTS')) {
            define('JPATH_TESTS', __DIR__);
        }

        // Don't report strict errors. This is needed because sometimes a test complains about arguments passed as reference
        ini_set('zend.ze1_compatibility_mode', '0');
        error_reporting(E_ALL & ~E_STRICT);
        ini_set('display_errors', 1);

        // Fix magic quotes on PHP 5.3
        if (version_compare(PHP_VERSION, '5.4.0', 'lt')) {
            ini_set('magic_quotes_runtime', 0);
        }

        // Fixed timezone to preserve our sanity
        @date_default_timezone_set('UTC');

        $siteroot = $this->getSiteRoot();

        // Set up the Joomla! environment
        if (file_exists($siteroot . '/defines.php')) {
            include_once $siteroot . '/defines.php';
        }

        if (! defined('_JDEFINES')) {
            if (! defined('JPATH_BASE')) {
                define('JPATH_BASE', $siteroot);
            }

            require_once JPATH_BASE . '/includes/defines.php';
        }

        // Bootstrap the CMS libraries.
        require_once JPATH_LIBRARIES . '/import.legacy.php';
        require_once JPATH_LIBRARIES . '/cms.php';

        $config = JFactory::getConfig($this->getConfigurationFile());

        $config->set('dbtype', JTEST_DB_ENGINE);
        $config->set('host', JTEST_DB_HOST);
        $config->set('db', JTEST_DB_NAME);
        $config->set('user', JTEST_DB_USER);
        $config->set('password', JTEST_DB_PASSWORD);
        $config->set('dbprefix', JTEST_DB_PREFIX);

        $config->set('tmp_path', JPATH_ROOT . '/tmp');
        $config->set('log_path', JPATH_ROOT . '/logs');

        // Despite its name, this is the session STORAGE, NOT the session HANDLER. Because that somehow makes sense. NOT.
        $config->set('session_handler', 'none');

        JFactory::$config = $config;

        // We need to set up the JSession object
        $sessionHandler = new \Weble\JoomlaTestBench\Session\TestSession();
        $session        = \JSession::getInstance('none', [], $sessionHandler);
        $input          = new TestInput([]);
        $dispatcher     = \JEventDispatcher::getInstance();
        $session->initialise($input, $dispatcher);

        JFactory::$session = $session;

        try {
            $application = new TestApplication($input);
        } catch (\JDatabaseExceptionExecuting $e) {
            $this->setupDb();
            $application = new TestApplication($input);
        }

        // Instantiate the application.
        JFactory::$application = $application;
        JFactory::$application->loadDispatcher($dispatcher);

        $r = new ReflectionClass(CMSApplication::class);
        $p = $r->getProperty('instances');
        $p->setAccessible(true);
        $p->setValue([
            'site' => $application,
        ]);

        $this->application = \JFactory::$application;

        return $this->application;
    }

    protected function setupDb()
    {
        $factory = JDatabaseFactory::getInstance();
        $driver  = $factory->getDriver(
            JTEST_DB_ENGINE,
            [
                'database' => JTEST_DB_NAME,
                'host'     => JTEST_DB_HOST,
                'user'     => JTEST_DB_USER,
                'password' => JTEST_DB_PASSWORD,
                'prefix'   => JTEST_DB_PREFIX,
            ]
        );

        $engine = JTEST_DB_ENGINE;
        if ($engine === 'mysqli') {
            $engine = 'mysql';
        }

        $dir   = realpath(__DIR__ . '/../../vendor/joomla/joomla-cms/installation/sql/' . $engine);
        $files = ['joomla.sql', 'sample_testing.sql'];

        foreach ($files as $file) {
            $sql     = file_get_contents($dir . '/' . $file);
            $queries = DbTestHelper::splitQueries($sql);

            if (! count($queries)) {
                continue;
            }

            foreach ($queries as $query) {
                $driver->setQuery($query);
                $driver->execute();
            }
        }
    }

    protected function afterApplicationCreated(callable $callback): void
    {
        $this->afterApplicationCreatedCallbacks[] = $callback;

        if ($this->setUpHasRun) {
            \call_user_func($callback);
        }
    }
}
