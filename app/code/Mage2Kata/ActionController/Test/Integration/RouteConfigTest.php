<?php

namespace Mage2Kata\ActionController;

use Magento\Framework\App\Route\ConfigInterface as RouteConfigInterface;
use Magento\Framework\App\Router\Base as BaseRouter;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Request;

class RouteConfigTest extends \PHPUnit_Framework_TestCase
{
	/** @var ObjectManager */
	protected $objectManager;

	protected function setUp()
	{
		$this->objectManager = ObjectManager::getInstance();
	}

	/**
	 * @magentoAppArea frontend
	 */
	public function testRouteIsConfigured()
	{
		/** @var RouteConfigInterface $routeConfig */
		$routeConfig = $this->objectManager->create( RouteConfigInterface::class );
		$this->assertContains( 'Mage2Kata_ActionController', $routeConfig->getModulesByFrontName( 'mage2kata' ) );
	}

	/**
	 * @magentoAppArea frontend
	 */
	public function testMage2KataIndexIndexActionControllerIsFound()
	{
		// Mock the request object
		/** @var Request $request */
		$request = $this->objectManager->create( Request::class );
		$request->setModuleName( 'mage2kata' )
		        ->setControllerName( 'index' )
		        ->setActionName( 'index' );

		// Ask the BaseRouter class to match our mock request to our controller action class
		/** @var BaseRouter $baseRouter */
		$baseRouter     = $this->objectManager->create( BaseRouter::class );
		$expectedAction = \Mage2Kata\ActionController\Controller\Index\Index::class;
		$this->assertInstanceOf( $expectedAction, $baseRouter->match( $request ) );
	}
}

