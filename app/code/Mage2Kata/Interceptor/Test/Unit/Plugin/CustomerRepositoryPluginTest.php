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
	/** @var  $_customerRepositoryPlugin CustomerRepositoryPlugin */
	protected $_customerRepositoryPlugin;

	/** @var  $_mockCustomerRepository CustomerRepositoryInterface */
	protected $_mockCustomerRepository;

	/**
	 * @var $_mockCustomerToBeSaved CustomerInterface
	 */
	protected $_mockCustomerToBeSaved;

	/**
	 * @var $_mockSavedCustomer CustomerInterface
	 */
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

//		$this->_mockExternalCustomerApi     = $this->getMock( ExternalCustomerApi::class, ['registerNewCustomer']);
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

//	public function testItNotifiesTheExternalApiForNewCustomers( )
//	{
//		$this->_mockCustomerToBeSaved->method('getId')->willReturn(null);
//	}
//}