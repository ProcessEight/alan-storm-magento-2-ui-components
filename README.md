# Mage2Katas

Work related to the [Mage2Katas](https://www.youtube.com/channel/UCRFDWo7jTlrpEsJxzc7WyPw) series.

## Environment setup

### Configuring PHPUnit

Use this sample `phpunit.xml` file:

```xml
// File: dev/tests/integration/phpunit.xml
<?xml version="1.0" encoding="UTF-8"?>
<!--
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
-->
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="http://schema.phpunit.de/4.1/phpunit.xsd"
         colors="true"
         bootstrap="./framework/bootstrap.php"
>
    <!-- Test suites definition -->
    <testsuites>
        <!-- Memory tests run first to prevent influence of other tests on accuracy of memory measurements -->
        <testsuite name="Memory Usage Tests">
            <file>testsuite/Magento/MemoryUsageTest.php</file>
        </testsuite>
        <testsuite name="Magento Integration Tests">
            <directory suffix="Test.php">testsuite</directory>
            <exclude>testsuite/Magento/MemoryUsageTest.php</exclude>
        </testsuite>
        <!-- Only run tests in custom modules -->
        <testsuite name="Mage2Kata Tests">
            <directory>../../../app/code/*/*/Test/*</directory>
            <exclude>../../../app/code/Magento</exclude>
        </testsuite>
    </testsuites>
    <!-- Code coverage filters -->
    <filter>
        <whitelist addUncoveredFilesFromWhiteList="true">
            <directory suffix=".php">../../../app/code/Magento</directory>
            <directory suffix=".php">../../../lib/internal/Magento</directory>
            <exclude>
                <directory>../../../app/code/*/*/Test</directory>
                <directory>../../../lib/internal/*/*/Test</directory>
                <directory>../../../lib/internal/*/*/*/Test</directory>
                <directory>../../../setup/src/*/*/Test</directory>
            </exclude>
        </whitelist>
    </filter>
    <!-- PHP INI settings and constants definition -->
    <php>
        <includePath>.</includePath>
        <includePath>testsuite</includePath>
        <ini name="date.timezone" value="Europe/London"/>
        <ini name="xdebug.max_nesting_level" value="200"/>
        <ini name="memory_limit" value="-1"/>
        <!-- Local XML configuration file ('.dist' extension will be added, if the specified file doesn't exist) -->
        <const name="TESTS_INSTALL_CONFIG_FILE" value="etc/install-config-mysql.php"/>
        <!-- Local XML configuration file ('.dist' extension will be added, if the specified file doesn't exist) -->
        <const name="TESTS_GLOBAL_CONFIG_FILE" value="etc/config-global.php"/>
        <!-- Semicolon-separated 'glob' patterns, that match global XML configuration files -->
        <const name="TESTS_GLOBAL_CONFIG_DIR" value="../../../app/etc"/>
        <!-- Whether to cleanup the application before running tests or not -->
        <const name="TESTS_CLEANUP" value="disabled"/>
        <!-- Memory usage and estimated leaks thresholds -->
        <!--<const name="TESTS_MEM_USAGE_LIMIT" value="1024M"/>-->
        <const name="TESTS_MEM_LEAK_LIMIT" value=""/>
        <!-- Whether to output all CLI commands executed by the bootstrap and tests -->
        <!--<const name="TESTS_EXTRA_VERBOSE_LOG" value="1"/>-->
        <!-- Path to Percona Toolkit bin directory -->
        <!--<const name="PERCONA_TOOLKIT_BIN_DIR" value=""/>-->
        <!-- CSV Profiler Output file -->
        <!--<const name="TESTS_PROFILER_FILE" value="profiler.csv"/>-->
        <!-- Bamboo compatible CSV Profiler Output file name -->
        <!--<const name="TESTS_BAMBOO_PROFILER_FILE" value="profiler.csv"/>-->
        <!-- Metrics for Bamboo Profiler Output in PHP file that returns array -->
        <!--<const name="TESTS_BAMBOO_PROFILER_METRICS_FILE" value="../../build/profiler_metrics.php"/>-->
        <!-- Magento mode for tests execution. Possible values are "default", "developer" and "production". -->
        <const name="TESTS_MAGENTO_MODE" value="developer"/>
        <!-- Minimum error log level to listen for. Possible values: -1 ignore all errors, and level constants form http://tools.ietf.org/html/rfc5424 standard -->
        <const name="TESTS_ERROR_LOG_LISTENER_LEVEL" value="-1"/>
        <!-- Connection parameters for MongoDB library tests -->
        <!--<const name="MONGODB_CONNECTION_STRING" value="mongodb://localhost:27017"/>-->
        <!--<const name="MONGODB_DATABASE_NAME" value="magento_integration_tests"/>-->
    </php>
    <!-- Test listeners -->
    <listeners>
        <listener class="Magento\TestFramework\Event\PhpUnit"/>
        <listener class="Magento\TestFramework\ErrorLog\Listener"/>
    </listeners>
</phpunit>
```
## Configuring PhpStorm
Create a new `Run Configuration`:
1. Go to `Run`, `Edit Configurations`.
1. Create a new `PHPUnit` configuration with the following values:
    * Name: `Mage2Katas Integration Test Rig`
    * Test Runner:
        * Test Scope: `Defined in the configuration file`
        * Use alternative configuration file: `/path/to/magento/root/dev/tests/integration/phpunit.xml`
        * Test Runner options: `--testsuite "Mage2Kata Tests"`
        
You can, of course, use different `phpunit.xml` config files for different types of test (integration, unit etc).

## 1. The Module Skeleton Kata

* This assumes that the Magento 2 test framework (including integration tests) and your IDE are already setup and configured to run tests.
    * Refer to the DevDocs for a quick guide on setting up integration tests [[3]][3] and on setting up PhpStorm with PHPUnit [[4]][4]
* Start with Integration tests first.
* Manually create the following folder structure module in the `app/code` directory:

```bash
app
    code
        [Vendor Name]
            [Module Name]
                Test
                    Integration
```

* Create your first test class and a 'test nothing' method. We'll use this empty test to check our framework and IDE are setup correctly:
```php
<?php

namespace Mage2Kata\ModuleSkeleton\Test\Integration;

class SkeletonModuleConfigTest extends \PHPUnit_Framework_TestCase
{
	public function testNothing()
	{
		$this->markTestSkipped('Testing that PhpStorm and test framework is setup correctly');
	}
}
```
The next step is to write the next most basic test: To check that the module exists according to Magento. 

In other words, we test for the existence of the `registration.php` file:

```php
private $moduleName = 'Mage2Kata_SkeletonModule';

public function testTheModuleIsRegistered()
{
    $registrar = new ComponentRegistrar();
    $this->assertArrayHasKey(
        $this->moduleName,
        $registrar->getPaths( ComponentRegistrar::MODULE)
    );
}
```
We've extracted the module name into a member variable so we can re-use it in other tests.

At this point you may get an error if you try to run the test. This is because Magento expects every module to have, at the bare minimum, a `registration.php` file and a `module.xml` file with a `setup_version` attribute. 

Let's now move onto the next step in creating a module - the `module.xml`.

Here's the test:
```php
public function testTheModuleIsConfiguredAndEnabled()
{
    /** @var ObjectManager $objectManager */
    $objectManager = ObjectManager::getInstance();

    /** @var ModuleList $moduleList */
    $moduleList = $objectManager->create( ModuleList::class);

    $this->assertTrue( $moduleList->has( $this->moduleName), 'The module is not enabled');
}
```

If we run this test now, it will fail. If we create the `module.xml` file, then it should pass.

```xml
// File: Mage2Kata/SkeletonModule/etc/module.xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Mage2Kata_SkeletonModule" setup_version="0.1.0">
    </module>
</config>
```

So that's our extremely basic module created using TDD.

## Sources
* [Running Unit Tests in the CLI](http://devdocs.magento.com/guides/v2.1/test/unit/unit_test_execution_cli.html)
* [Running Unit Tests in PHPStorm](http://devdocs.magento.com/guides/v2.1/test/unit/unit_test_execution_phpstorm.html)

[1]: http://magento.stackexchange.com/questions/140314/magento-2-unit-test-with-mock-data-dont-work-why/140337#140337
[2]: http://devdocs.magento.com/guides/v2.1/test/unit/writing_testable_code.html
[3]: http://devdocs.magento.com/guides/v2.1/test/integration/integration_test_setup.html
[4]: http://devdocs.magento.com/guides/v2.1/install-gde/docker/docker-phpstorm-project.html