<?php

namespace Weble\JoomlaTestBench\Concerns;

use JFactory;

trait DatabaseTransactions
{
    public function beginDatabaseTransaction()
    {
        $database = JFactory::getDbo();
        $database->transactionStart();

        JFactory::$application->registerEvent('onTestFinished', function () use ($database) {
            $database->transactionRollback();
        });
    }
}
