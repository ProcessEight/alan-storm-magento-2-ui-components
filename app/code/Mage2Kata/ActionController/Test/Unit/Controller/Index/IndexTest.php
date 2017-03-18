<?php
/**
 * Created by PhpStorm.
 * User: zone8
 * Date: 18/03/17
 * Time: 16:31
 */

namespace Mage2Kata\ActionController\Controller\Index;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\Controller\Result\Raw as RawResult;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class IndexTest extends \PHPUnit_Framework_TestCase
{
	/** @var Index */
	protected $controller;

	/** @var RawResult|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockRawResult;

	protected function setUp()
	{
		// Mock the Raw result object
		$this->_mockRawResult = $this->getMock( RawResult::class );

		// Mock the Result Factory
		/** @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject $mockRawResultFactory */
		$mockRawResultFactory = $this->getMock(ResultFactory::class, ['create'], [], '', false);

		// Set our expectation (when we call ResultFactory::create(ResultFactory::TYPE_RAW) we expect to get a RawResult object back)
		$mockRawResultFactory->method('create')->with(ResultFactory::TYPE_RAW)->willReturn($this->_mockRawResult);

		/** @var ActionContext|\PHPUnit_Framework_MockObject_MockObject $mockContext */
		$mockContext = $this->getMock(ActionContext::class, [], [], '', false);

		$this->controller = new Index($mockContext, $mockRawResultFactory);
	}

	public function testReturnsResultInstance()
	{
		$this->assertInstanceOf( ResultInterface::class, $this->controller->execute() );
	}
}
