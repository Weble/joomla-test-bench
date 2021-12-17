# Joomla Testing Helper for Extensions and Sites Development 

Joomla Test Bench is a package that makes it easy to test a Joomla Extension or a Joomla website by using Integration and Feature tests.

Clearly inspired by Laravel's [Orchestral Testbench Package](https://github.com/orchestral/testbench), its goal is to give the site / extension developer a set of tools to develop meaning tests, without having to rely on super complex setups.

## Installation

```composer require --dev weble/joomla-test-bench```

## Site Testing Examples

Install this package with composer in your site's root.

```
cd /var/www/yoursite
composer require --dev weble/joomla-test-bench
``` 

Proceed to create a new ```phpunit.xml``` file inside your root directory, and configure the db variables to a local version of the site's db.

Usually, it's a good idea to clone the site's db and use it for testing,

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
        printerClass="NunoMaduro\Collision\Adapters\Phpunit\Printer"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
        bootstrap="vendor/autoload.php"
        colors="true"
>
    <testsuites>
        <testsuite name="MT">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
    <php>
        <const name="JTEST_DB_ENGINE" value="mysqli" />
        <const name="JTEST_DB_HOST" value="localhost" />
        <const name="JTEST_DB_NAME" value="testing" />
        <const name="JTEST_DB_USER" value="root" />
        <const name="JTEST_DB_PASSWORD" value="" />
    </php>
</phpunit>

```

And then create a new folder to hold your tests.
Inside it, create your first PHPUnit tests, as you probably already accustomed to.

```php
<?php


namespace Your\Site\Tests;


class ExampleTest extends \Weble\JoomlaTestBench\TestCase
{
    /** @test */
    public function it_can_see_the_homepage()
    {
        $this->assertTrue($this->get('/index.php')->successful());
    }
}

```

The package will automatically detect that it's in a local joomla install, and use the testing db you specified in your phpunit.xml configuration.

Then it will "fake" the request going through the application, and let you test the response by accessing the status code, the body, and the headers.


## Extension Testing (WIP)

To test a joomla extension, install this package in the extension repository through composer

```composer require --dev weble/joomla-test-bench```

Then proceed to creating a dedicated ```phpunit.xml``` file

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
        bootstrap="vendor/autoload.php"
        colors="true"
>
    <testsuites>
        <testsuite name="MT">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
    <php>
        <const name="JTEST_DB_ENGINE" value="mysqli" />
        <const name="JTEST_DB_HOST" value="localhost" />
        <const name="JTEST_DB_NAME" value="testing" />
        <const name="JTEST_DB_USER" value="root" />
        <const name="JTEST_DB_PASSWORD" value="" />
    </php>
</phpunit>
```

Setup a local db to use as a testing ground.
The package will detect that there is no local installation of joomla available, and use the latest joomla version included with this package (3.10.x for now) and the db you provided, installing the testing data that comes with joomla.

## TODO

- [ ] Provide a way to let an extension developer "install" a set of required extensions (like "i need akeeba backup for my extension to work") by using ```registerExtension($pathToZip)``` or something similar in the basic test case.
- [ ] Provide a quick way to register the extension and install it in the provided application
- [ ] Integrate Dusk (or similar browser testing tool)
- [ ] Provide more test helpers (->assertDatabaseHas, etc) 
