<?php
/**
 * Created by PhpStorm.
 * User: zone8
 * Date: 17/03/17
 * Time: 19:34
 */

namespace Mage2Kata\Interceptor\Plugin;


use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerRepositoryPlugin
{
	/**
	 * @var ExternalCustomerApi
	 */
	private $customerApi;

	/**
	 * CustomerRepositoryPlugin constructor.
	 */
	public function __construct( ExternalCustomerApi $customerApi )
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
		$savedCustomer = $proceed( $customer, $passwordHash );
		if ( $this->isCustomerNew( $customer ) ) {
			$this->customerApi->registerNewCustomer( $savedCustomer->getId() );
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