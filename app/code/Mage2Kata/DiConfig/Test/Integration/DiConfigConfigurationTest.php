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
		$virtualType = Model\Config\Data\Virtual::class;
		$this->assertVirtualType( \Magento\Framework\Config\Data::class, $virtualType );
		$this->assertDiArgumentSame( 'mage2kata_unitmap_config', $virtualType, 'cacheId' );

		$argumentName = 'reader';
		$expectedType = Model\Config\Data\Reader::class;

		$arguments = $this->getDiConfig()->getArguments( $virtualType );
		if ( ! isset( $arguments[ $argumentName ] ) ) {
			$this->fail( sprintf( 'No argument "%s" configured for %s', $argumentName, $virtualType ) );
		}
		if ( ! isset( $arguments[ $argumentName ][ 'instance' ] ) ) {
			$this->fail( sprintf( 'Argument "%s" for %s is not xsi:type="object"', $argumentName, $virtualType ) );
		}
		$this->assertSame( $expectedType, $arguments[$argumentName]['instance']);
	}

	/**
	 * @return ObjectManagerConfig
	 */
	protected function getDiConfig(): ObjectManagerConfig
	{
		$diConfig = ObjectManager::getInstance()->get( ObjectManagerConfig::class );

		return $diConfig;
	}

	/**
	 * @param string $expectedType
	 * @param string $virtualType
	 */
	protected function assertVirtualType( $expectedType, $virtualType )
	{
		$this->assertSame( $expectedType, $this->getDiConfig()->getInstanceType( $virtualType ) );
	}

	/**
	 * @param string $expected
	 * @param string $virtualType
	 * @param string $argumentName
	 */
	protected function assertDiArgumentSame( $expected, $virtualType, $argumentName )
	{
		$arguments = $this->getDiConfig()->getArguments( $virtualType );
		if ( ! isset( $arguments[ $argumentName ] ) ) {
			$this->fail( sprintf( 'No argument "%s" configured for %s', $argumentName, $virtualType ) );

		}
		$this->assertSame( $expected, $arguments[ $argumentName ] );
	}


}