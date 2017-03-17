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