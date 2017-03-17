# Mage2Katas

Work related to the [Mage2Katas](https://www.youtube.com/channel/UCRFDWo7jTlrpEsJxzc7WyPw) series.

## Environment

Copy the `install-config-mysql-php.dist` file and update the database connection details accordingly:
```bash
zone8@zone8-aurora-r5:/var/www/vhosts/magento2.localhost.com$ cp -f dev/tests/integration/etc/install-config-mysql.php.dist dev/tests/integration/etc/install-config-mysql.php
```

## The Module Skeleton Kata

## The Plugin Config Kata

## The Around Interceptor Kata

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