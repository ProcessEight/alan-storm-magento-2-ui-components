<?php
/**
 * Created by PhpStorm.
 * User: zone8
 * Date: 17/03/17
 * Time: 16:23
 */

namespace Mage2Kata\Interceptor\Plugin;


use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPluginTest extends \PHPUnit_Framework_TestCase
{
	/** @var  CustomerRepositoryPlugin */
	protected $_customerRepositoryPlugin;

	/** @var  CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockCustomerRepository;

	/**
	 * @var CustomerInterface|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected $_mockCustomerToBeSaved;

	/**
	 * @var CustomerInterface|\PHPUnit_Framework_MockObject_MockObject
	 */
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
		$this->_mockExternalCustomerApi  = $this->getMock( ExternalCustomerApi::class, [ 'registerNewCustomer' ] );
		$this->_customerRepositoryPlugin = new CustomerRepositoryPlugin( $this->_mockExternalCustomerApi );
	}

	protected function callAroundSavePlugin()
	{
		$subject      = $this->_mockCustomerRepository;
		$proceed      = $this;
		$customer     = $this->_mockCustomerToBeSaved;
		$passwordHash = null;

		return $this->_customerRepositoryPlugin->aroundSave( $subject, $proceed, $customer, $passwordHash );
	}

	public function testItCanBeInstantiated()
	{
		$this->_customerRepositoryPlugin;
	}

	public function testTheAroundSaveMethodCanBeCalled()
	{
		$this->assertSame( $this->_mockSavedCustomer, $this->callAroundSavePlugin() );
	}

	public function testItNotifiesTheExternalApiForNewCustomers()
	{
		$customerId = 123;

		// The getId() method of the customer to be saved will return null because it has not been saved yet
		$this->_mockCustomerToBeSaved->method( 'getId' )->willReturn( null );

		// Once our customer has been saved, it will have an ID, which we can then pass to the registerNewCustomer method
		$this->_mockSavedCustomer->method( 'getId' )->willReturn( $customerId );

		// The registerNewCustomer method of the API is expected to be called exactly once, because a customer can only register once
		$this->_mockExternalCustomerApi->expects( $this->once() )->method( 'registerNewCustomer' )->with( $customerId );

		// Now call the plugin so PHPUnit can test it
		$this->callAroundSavePlugin();
	}

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
