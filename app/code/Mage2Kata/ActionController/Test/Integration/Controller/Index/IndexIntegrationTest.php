<?php

namespace Mage2Kata\ActionController\Controller\Index;

use Magento\TestFramework\Request;
use Magento\TestFramework\TestCase\AbstractController;

class IndexIntegrationTest extends AbstractController
{
//	public function testEnvironmentIsSetupCorrectly()
//	{
//		$this->markTestSkipped('Test environment is setup correctly');
//	}

	/**
	 * Test that we can actually load the controller action
	 */
	public function testCanHandleGetRequests()
	{
		$this->getRequest()->setMethod( Request::METHOD_GET );
		$this->dispatch( 'mage2kata/index/index' );
		$this->assertSame( 200, $this->getResponse()->getHttpResponseCode() );
		$this->assertContains( '<body', $this->getResponse()->getBody() );
	}

	/**
	 * Test that we can only make GET requests to controller action (for that is what this scenario requires)
	 */
	public function testCannotHandlePostRequests()
	{
		$this->getRequest()->setMethod( Request::METHOD_POST );
		$this->dispatch( 'mage2kata/index/index' );
		$this->assertSame( 404, $this->getResponse()->getHttpResponseCode() );
		$this->assert404NotFound();
	}
}