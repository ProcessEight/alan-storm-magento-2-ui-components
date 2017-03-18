# Mage2Katas

Work related to the [Mage2Katas](https://www.youtube.com/channel/UCRFDWo7jTlrpEsJxzc7WyPw) series.

* [Environment setup](#environment-setup)
    * [Configuring PHPUnit](#configuring-phpunit)
    * [Configuring PhpStorm](#configuring-phpstorm)
    * [Configure the database (for integration tests)](#configure-the-database-for-integration-tests)
* [1. The Module Skeleton Kata](#1-the-module-skeleton-kata)
* [2. The Plugin Config Kata](#2-the-plugin-config-kata)
* [3. The Around Interceptor Kata](#3-the-around-interceptor-kata)
* [Sources](#sources)

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
### Configuring PhpStorm
Create a new `Run Configuration`:
1. Go to `Run`, `Edit Configurations`.
1. Create a new `PHPUnit` configuration with the following values:
    * Name: `Mage2Katas Integration Test Rig`
    * Test Runner:
        * Test Scope: `Defined in the configuration file`
        * Use alternative configuration file: `/path/to/magento/root/dev/tests/integration/phpunit.xml`
        * Test Runner options: `--testsuite "Mage2Kata Tests"`
        
You can, of course, use different `phpunit.xml` config files for different types of test (integration, unit etc).

### Configure the database (for integration tests)

Copy the `install-config-mysql-php.dist` file and update the database connection details accordingly:
```bash
zone8@zone8-aurora-r5:/var/www/vhosts/magento2.localhost.com$ cp -f dev/tests/integration/etc/install-config-mysql.php.dist dev/tests/integration/etc/install-config-mysql.php
```

## Troubleshooting

### Integration tests not behaving as expected

Remember to clear the integration test cache if you've disabled the `TESTS_CLEANUP` environment variable:
```bash
zone8@zone8-aurora-r5:/var/www/vhosts/magento2.localhost.com$ rm -rf dev/tests/integration/tmp/sandbox-*
```

## 1. The Module Skeleton Kata [5]

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

## 2. The Plugin Config Kata [6]

Awaiting merge in from MacBook.

## 3. The Around Interceptor Kata [7]

An around plugin should have these tests as a minimum:
* A test that ensures the plugin class can be instantiated
* A test that ensures the right return value is passed
* A test that ensures the business logic of the plugin does what it is supposed to (i.e. That specific methods are called the required number of times with the required arguments)
* If something should not happen based on passed arguments, that should be tested as well (e.g. A customer registration method should not be called for registered customers)
* A test that ensures that any specific exceptions that the wrapped method throws are of the right type (e.g. `CustomerRepositoryInterface::save()` throws a `\Magento\Framework\Exception\State\InputMismatchException` If the provided customer email already exists)

Create the Plugin test:

```php
// File: app/code/Mage2Kata/Interceptor/Test/Unit/Plugin/CustomerRepositoryPluginTest.php
<?php

namespace Mage2Kata\Interceptor\Plugin;

class CustomerRepositoryPluginTest extends \PHPUnit_Framework_TestCase
{
	public function testItCanBeInstantiated()
	{
		new CustomerRepositoryPlugin();
	}
}
```

Now create the plugin class which satisfies the test:
```php
// File: /var/www/vhosts/magento2.localhost.com/app/code/Mage2Kata/Interceptor/Plugin/CustomerRepositoryPlugin.php
<?php

namespace Mage2Kata\Interceptor\Plugin;

class CustomerRepositoryPlugin
{
	
}
```
The test succeeds.

Now let's add the test for the next step: Adding the plugin method itself:
```php
// File: /var/www/vhosts/magento2.localhost.com/app/code/Mage2Kata/Interceptor/Test/Unit/Plugin/CustomerRepositoryPluginTest.php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPluginTest extends \PHPUnit_Framework_TestCase
{
	/** @var  $_customerRepositoryPlugin CustomerRepositoryPlugin */
	protected $_customerRepositoryPlugin;

	/** @var  $_mockCustomerRepository CustomerRepositoryInterface */
	protected $_mockCustomerRepository;

	/**
	 * @var $_mockCustomerToBeSaved CustomerInterface
	 */
	protected $_mockCustomerToBeSaved;

	public function __invoke(CustomerInterface $customer, $passwordHash)
	{
	}

	protected function setUp()
	{
		$this->_mockCustomerRepository      = $this->getMock( CustomerRepositoryInterface::class);
		$this->_mockCustomerToBeSaved       = $this->getMock( CustomerInterface::class);
		$this->_customerRepositoryPlugin    = new CustomerRepositoryPlugin();
	}

	public function testItCanBeInstantiated()
	{
		$this->_customerRepositoryPlugin;
	}

	public function testTheAroundSaveMethodCanBeCalled()
	{
		$subject        = $this->_mockCustomerRepository;
		$proceed        = $this;
		$customer       = $this->_mockCustomerToBeSaved;
		$passwordHash   = null;
		$this->_customerRepositoryPlugin->aroundSave($subject, $proceed, $customer, $passwordHash);
	}
}
```

We've extracted the `customerRepositoryPlugin` object into a member variable and setup the necessary mock objects (`_mockCustomerRepository`, `_mockCustomerToBeSaved`).

We don't need to mock `passwordHash` because it's a simple scalar value (a string).

Note how, for the `proceed` parameter, we make our test class callable by adding the `__invoke` PHP magic method and then assigning a reference to the class `this` to it. This basically makes the test class a mock object in itself.

The test fails. So let's make it pass by adding the `aroundSave` method to the `CustomerRepositoryPlugin` class.

***Pro tip:*** You can auto-generate the method by pressing `Alt+Return` whilst on the method name.

```php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPlugin
{
	public function aroundSave( $subject, $proceed, $customer, $passwordHash )
	{
		
	}
}
```

The `CustomerRepositoryInterface::save()` method returns a `CustomerInterface` object. Let's write a new test to make sure it does.

```php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPluginTest extends \PHPUnit_Framework_TestCase
{
	/** @var  $_customerRepositoryPlugin CustomerRepositoryPlugin */
	protected $_customerRepositoryPlugin;

	/** @var  $_mockCustomerRepository CustomerRepositoryInterface */
	protected $_mockCustomerRepository;

	/** @var $_mockCustomerToBeSaved CustomerInterface */
	protected $_mockCustomerToBeSaved;

	/** @var $_mockSavedCustomer CustomerInterface */
	protected $_mockSavedCustomer;

	public function __invoke(CustomerInterface $customer, $passwordHash)
	{
		return $this->_mockSavedCustomer;
	}

	protected function setUp()
	{
		$this->_mockCustomerRepository      = $this->getMock( CustomerRepositoryInterface::class);
		$this->_mockCustomerToBeSaved       = $this->getMock( CustomerInterface::class);
		$this->_mockSavedCustomer           = $this->getMock( CustomerInterface::class);
		$this->_customerRepositoryPlugin    = new CustomerRepositoryPlugin();
	}

	protected function callAroundSavePlugin()
	{
		$subject      = $this->_mockCustomerRepository;
		$proceed      = $this;
		$customer     = $this->_mockCustomerToBeSaved;
		$passwordHash = null;
		return $this->_customerRepositoryPlugin->aroundSave( $subject, $proceed, $customer, $passwordHash );
	}

	public function testTheAroundSaveMethodCanBeCalled()
	{
		$result = $this->callAroundSavePlugin();
		$this->assertSame( $this->_mockSavedCustomer, $result);
	}
}
```
Here we mock a new object, `_mockSavedCustomer`, then compare it against what the `aroundSave` method actually returns.

The test fails. To make it pass we need to make our plugin return a `CustomerInterface` object. Here is the code to make it pass:

```php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPlugin
{
	public function aroundSave(
		CustomerRepositoryInterface $subject,
		callable $proceed,
		CustomerInterface $customer,
		$passwordHash = null
	)
	{
		return $proceed($customer, $passwordHash);
	}
}
```
The test succeeds again. Of course, this plugin doesn't actually do anything yet. The purpose of this plugin is to call an API method, which should only be called when a new customer registers.

Let's write a test that ensures that that API method is called and called exactly once (because a customer can't register twice).

```php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPluginTest extends \PHPUnit_Framework_TestCase
{
	/** @var  CustomerRepositoryPlugin */
	protected $_customerRepositoryPlugin;

	/** @var  CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockCustomerRepository;

	/** @var CustomerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockCustomerToBeSaved;

	/** @var CustomerInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockSavedCustomer;

	/** @var ExternalCustomerApi|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockExternalCustomerApi;

	public function __invoke( CustomerInterface $customer, $passwordHash )
	{
		return $this->_mockSavedCustomer;
	}

	protected function setUp()
	{
		$this->_mockCustomerRepository   = $this->getMock( CustomerRepositoryInterface::class );
		$this->_mockCustomerToBeSaved    = $this->getMock( CustomerInterface::class );
		$this->_mockSavedCustomer        = $this->getMock( CustomerInterface::class );
		$this->_customerRepositoryPlugin = new CustomerRepositoryPlugin($this->_mockExternalCustomerApi);

		$this->_mockExternalCustomerApi = $this->getMock( ExternalCustomerApi::class, [ 'registerNewCustomer' ] );
	}

	protected function callAroundSavePlugin()
	{
		$subject      = $this->_mockCustomerRepository;
		$proceed      = $this;
		$customer     = $this->_mockCustomerToBeSaved;
		$passwordHash = null;

		return $this->_customerRepositoryPlugin->aroundSave( $subject, $proceed, $customer, $passwordHash );
	}

	public function testItNotifiesTheExternalApiForNewCustomers()
	{
		// The getId() method of the customer to be saved will return null because it has not been saved yet
		$this->_mockCustomerToBeSaved->method( 'getId' )->willReturn( null );

		// The registerNewCustomer method of the API is expected to be called exactly once, because a customer can only register once
		$this->_mockExternalCustomerApi->expects( $this->once() )->method( 'registerNewCustomer' );
	}
}
```
Note the following things:
* We are writing a test to test the functionality of a class and method that hasn't been written yet. The test defines the functionality of the method, thus ensuring code coverage of the class and method when we do write it (in the next step).
* The previous mocks we've created have been mocks of existing, core, classes. The class for our API, `ExternalCustomerApi`, doesn't exist yet, so we need to tell PHPUnit which methods it has before so it knows about them (PHPUnit knows about the methods in the other mock objects because it uses Reflection to detect which methods a class has).
* We want to call methods on our API object inside the `aroundSave()` plugin method, so we'll need to inject our `ExternalCustomerApi` object into the plugin class. Hence, we add our `_mockExternalCustomerApi` object to the `CustomerRepositoryPlugin` class constructor.

The test fails. To make it pass we need to inject the `ExternalCustomerApi` object into the `CustomerRepositoryPlugin` constructor and we need to call the `registerNewCustomer()` method in the `aroundSave()` plugin method exactly once. Let's do that:

***Pro tip:*** PhpStorm can generate the constructor for you by pressing `Ctrl+N`.

```php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPlugin
{
	/** @var ExternalCustomerApi */
	private $customerApi;

	/**
	 * CustomerRepositoryPlugin constructor.
	 */
	public function __construct(ExternalCustomerApi $customerApi)
	{
		$this->customerApi = $customerApi;
	}

	public function aroundSave(
		CustomerRepositoryInterface $subject,
		callable $proceed,
		CustomerInterface $customer,
		$passwordHash = null
	)
	{
		$this->customerApi->registerNewCustomer();
		return $proceed($customer, $passwordHash);
	}
}
```

Now let's add a test that ensures the `registerNewCustomer()` method is not called when existing customers are saved.

```php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPluginTest extends \PHPUnit_Framework_TestCase
{
    // ... everything else ...
    
	public function testItDoesNotifyTheExternalApiForExistingCustomers()
	{
		// The getId() method of the customer to be saved will return null because it has not been saved yet
		$this->_mockCustomerToBeSaved->method( 'getId' )->willReturn( 23 );

		// The registerNewCustomer method of the API is expected to be called exactly once, because a customer can only register once
		$this->_mockExternalCustomerApi->expects( $this->never() )->method( 'registerNewCustomer' );

		// Now call the plugin so PHPUnit can test it
		$this->callAroundSavePlugin();
	}
}
```
The test fails because the `registerNewCustomer()` methods is always being called. Let's add a check to make sure it only gets called for new customers:
```php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPlugin
{
    // ... other methods ...
    
	public function aroundSave(
		CustomerRepositoryInterface $subject,
		callable $proceed,
		CustomerInterface $customer,
		$passwordHash = null
	)
	{
		// Only register customer if they are new
		if($customer->getId() == null) {
			$this->customerApi->registerNewCustomer();
		}
		return $proceed($customer, $passwordHash);
	}
}
```
With this change, our test is back to green again.

Now it would make sense to pass in a customer ID to `registerNewCustomer()`. In our little test scenario, we can assume that a customer ID is all the `registerNewCustomer()` method needs to actually register a new customer.

Let's update our `testItNotifiesTheExternalApiForNewCustomers()` to reflect this requirement:
```php

	public function testItNotifiesTheExternalApiForNewCustomers()
	{
		$customerId = 123;

		// The getId() method of the customer to be saved will return null because it has not been saved yet
		$this->_mockCustomerToBeSaved->method( 'getId' )->willReturn( null );

		// Once our customer has been saved, it will have an ID, which we can then pass to the registerNewCustomer method
		$this->_mockSavedCustomer->method( 'getId')->willReturn( $customerId );

		// The registerNewCustomer method of the API is expected to be called exactly once, because a customer can only register once
		$this->_mockExternalCustomerApi->expects( $this->once() )->method( 'registerNewCustomer' )->with( $customerId );

		// Now call the plugin so PHPUnit can test it
		$this->callAroundSavePlugin();
	}

```
Our test fails again. We modify the `CustomerRepositoryPlugin` to satisfy the test:
```php
<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPlugin
{
    // ... other methods ...
    
	public function aroundSave(
		CustomerRepositoryInterface $subject,
		callable $proceed,
		CustomerInterface $customer,
		$passwordHash = null
	)
	{
		$savedCustomer = $proceed( $customer, $passwordHash );
		if( $this->isCustomerNew( $customer ) ) {
			$this->customerApi->registerNewCustomer($savedCustomer->getId());
		}

		return $savedCustomer;
	}

	/**
	 * @param CustomerInterface $customer
	 *
	 * @return bool
	 */
	protected function isCustomerNew( CustomerInterface $customer ): bool
	{
		return $customer->getId() == null;
	}
}
```
The test is now green again.

## The Plugin Integration Test Kata [8]

We'll create an integration test from scratch, using some of Magento's built-in annotations to help us.

The plugin we're testing expects two objects to be present: `CustomerRepositoryInterface` and `CustomerInterface`. We'll need to mock these for our test:
```php
	/**
	 * @magentoDataFixture Magento/Customer/_files/customer.php
	 */
	public function testTheExternalApiIsCalledWhenANewCustomerIsSaved()
	{
		/** @var CustomerRepositoryInterface $customerRepository */
		$customerRepository = $this->objectManager->create( CustomerRepositoryInterface::class );

		$customer = $customerRepository->get( 'customer@example.com' );

		$customerRepository->save( $customer );
	}
```
Things to note:
* We use the `@magentoDataFixture` annotation to specify a customer fixture rather than mocking the customer in the test class.
* Magento stores these fixtures in folders using the naming convention `_files`.
* If you look at one of these files (e.g. `Magento/Customer/_files/customer.php`), you'll see it's just a data install script.
* The filepath for these fixtures is relative to `/dev/tests/integration/testsuite/`

The test succeeds because it does not call the plugin (we have configured the plugin in previous example to only run in the `webapi_rest` scope).

In an integration test, you would normally test actual instances of classes rather than mocks. However, we don't have a concrete implementation of our API class (`ExternalCustomerApi`), so, for the purposes of this example, we'll mock it rather than creating it (in a real project, we would've created the class and written unit tests for it already):
```php

	/**
	 * @magentoDataFixture Magento/Customer/_files/customer.php
	 */
	public function testTheExternalApiIsCalledWhenANewCustomerIsSaved()
	{
		$this->setMagentoArea( Area::AREA_WEBAPI_REST );

		$mockExternalCustomerApi = $this->getMock( ExternalCustomerApi::class, ['registerNewCustomer']);
		$this->objectManager->configure( [ExternalCustomerApi::class => ['shared' => true]]);
		$this->objectManager->addSharedInstance( $mockExternalCustomerApi, ExternalCustomerApi::class);

		/** @var CustomerRepositoryInterface $customerRepository */
		$customerRepository = $this->objectManager->create( CustomerRepositoryInterface::class );

		$customer = $customerRepository->get( 'customer@example.com' );

		$customerRepository->save( $customer );
	}
```
Things to note:
* We set the Magento application area to `webapi_rest` to trigger our plugin.
* We define the `registerNewCustomer` method as a parameter when mocking the class because the class does not exist, so PHPUnit will not be able to use Reflection on it to determine what methods the class has.
* We tell Magento to always use our mock by telling the object manager to instantiate the object with the `shared` parameter. This makes the object behave like a singleton.

This isn't a real test though, because there are no assertions or expectations. Let's add some:
```php
/**
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
public function testTheExternalApiIsCalledWhenANewCustomerIsSaved()
{
    $this->setMagentoArea( Area::AREA_WEBAPI_REST );

    $mockExternalCustomerApi = $this->getMock( ExternalCustomerApi::class, ['registerNewCustomer']);
    $mockExternalCustomerApi->expects( $this->once())->method( 'registerNewCustomer');
    $this->objectManager->configure( [ExternalCustomerApi::class => ['shared' => true]]);
    $this->objectManager->addSharedInstance( $mockExternalCustomerApi, ExternalCustomerApi::class);

    /** @var CustomerRepositoryInterface $customerRepository */
    $customerRepository = $this->objectManager->create( CustomerRepositoryInterface::class );

    $customer = $customerRepository->get( 'customer@example.com' );
    $customer->setId(null);
    $customer->setEmail('another-customer@example.com');

    $customerRepository->save( $customer );
}
```
Things to note:
* We add a new expectation on the customer mock which says 'registerNewCustomer should be called exactly once'.
* We set the ID to null, because the customer is already registered and therefore already has an ID. So we trick Magento by resetting the ID, which is enough to make it think the mock is a new, unregistered customer.
* We also supply a new email address to prevent any 'email address already registered' warnings.

With the new data we set on the mock, the test succeeds.

Using the `@magentoDataFixture` annotation means that the fixture and the test are run inside a transaction. Once the test finishes, the transaction is rolled back, so we don't need to do any cleanup (like deleting the customers we created).

If you want to run a test inside a transaction without using fixtures, you can use the `@magentodbIsolation enabled` annotation instead.

## 5. The Route Config Kata [9]



## Sources
* [Running Unit Tests in the CLI](http://devdocs.magento.com/guides/v2.1/test/unit/unit_test_execution_cli.html)
* [Running Unit Tests in PHPStorm](http://devdocs.magento.com/guides/v2.1/test/unit/unit_test_execution_phpstorm.html)

[1]: http://magento.stackexchange.com/questions/140314/magento-2-unit-test-with-mock-data-dont-work-why/140337#140337
[2]: http://devdocs.magento.com/guides/v2.1/test/unit/writing_testable_code.html
[3]: http://devdocs.magento.com/guides/v2.1/test/integration/integration_test_setup.html
[4]: http://devdocs.magento.com/guides/v2.1/install-gde/docker/docker-phpstorm-project.html
[5]: http://vinaikopp.com/2016/02/05/01_the_skeleton_module_kata/
[6]: http://vinaikopp.com/2016/02/05/02_the_plugin_config_kata/
[7]: http://vinaikopp.com/2016/02/22/03_the_around_interceptor_kata/
[8]: http://vinaikopp.com/2016/03/07/04_the_plugin_integration_test_kata/
[9]: http://vinaikopp.com/2016/03/21/05_the_route_config_kata/