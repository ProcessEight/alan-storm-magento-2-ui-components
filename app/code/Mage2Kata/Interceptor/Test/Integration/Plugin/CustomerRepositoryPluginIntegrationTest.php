<?php

namespace Mage2Kata\Interceptor\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\TestFramework\App\State as AppState;
use Magento\TestFramework\Interception\PluginList;
use Magento\TestFramework\ObjectManager;

class CustomerRepositoryPluginIntegrationTest extends \PHPUnit_Framework_TestCase
{
	protected $pluginName = 'mage2kata_interceptor';
	/**
	 * @var ObjectManager
	 */
	private $objectManager;

	protected function setUp()
	{
		$this->objectManager = ObjectManager::getInstance();
	}

	protected function tearDown()
	{
		$this->setMagentoArea( null );
	}

	/**
	 * @param string $areaCode
	 */
	private function setMagentoArea( $areaCode )
	{
		/** @var AppState $applicationState */
		$applicationState = $this->objectManager->get( AppState::class );
		$applicationState->setAreaCode( $areaCode );
	}

	/**
	 * @return array[]
	 */
	protected function getCustomerRepositoryInterfacePluginInfo()
	{
		/** @var PluginList $pluginList */
		$pluginList = $this->objectManager->create( PluginList::class );

		$pluginInfo = $pluginList->get( CustomerRepositoryInterface::class, [] );

		return $pluginInfo;
	}

	public function testTheCustomerRepositoryPluginIsRegisteredInTheWebapiRestScope()
	{
		$this->setMagentoArea( Area::AREA_WEBAPI_REST );

		/** @var PluginList $pluginList */
		$pluginInfo = $this->getCustomerRepositoryInterfacePluginInfo();
		$this->assertSame( CustomerRepositoryPlugin::class, $pluginInfo[ $this->pluginName ]['instance'] );
	}

	public function testTheCustomerRepositoryPluginIsNotRegisteredInGlobalScope()
	{
		$this->setMagentoArea( Area::AREA_GLOBAL );
		$pluginInfo = $this->getCustomerRepositoryInterfacePluginInfo();


		$this->assertArrayNotHasKey( $this->pluginName, $pluginInfo );
	}

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

}