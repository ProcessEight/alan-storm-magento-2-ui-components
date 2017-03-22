<?php

namespace Mage2Kata\DiConfig;

use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerConfig;
use Magento\TestFramework\ObjectManager;

class DiConfigConfigurationTest extends \PHPUnit_Framework_TestCase
{
//	public function testEnvironmentIsSetupCorrectly()
//	{
//		$this->assertTrue( true );
//	}

	/**
	 * Test that the mapping of a virtual type to an actual type (i.e. class) is correctly configured
	 */
	public function testConfigDataVirtualType()
	{
		/** @var ObjectManagerConfig $diConfig */
		$diConfig = ObjectManager::getInstance()->get(ObjectManagerConfig::class);

		$virtualType = Model\Config\Data\Virtual::class;
		$expectedType = \Magento\Framework\Config\Data::class;

		$this->assertSame( $expectedType, $diConfig->getInstanceType( $virtualType));
	}
}