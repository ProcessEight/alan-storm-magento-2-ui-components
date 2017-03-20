<?php
/**
 * Created by PhpStorm.
 * User: zone8
 * Date: 18/03/17
 * Time: 16:31
 */

namespace Mage2Kata\ActionController\Controller\Index;

use Mage2Kata\ActionController\Model\Exception\RequiredArgumentMissingException;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\Controller\Result\Raw as RawResult;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;

class IndexTest extends \PHPUnit_Framework_TestCase
{
	/** @var UseCase|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockUseCase;

	/** @var Index */
	protected $controller;

	/** @var RawResult|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockRawResult;

	/** @var Request|\PHPUnit_Framework_MockObject_MockObject */
	protected $_mockRequest;

	protected function setUp()
	{
		// Mock the Raw result object
		$this->_mockRawResult = $this->getMock( RawResult::class );

		// Mock the Result Factory. The following two methods of doing so are equivalent.
		/** @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject $mockRawResultFactory */
//		$mockRawResultFactory = $this->getMock(ResultFactory::class, ['create'], [], '', false);
		$mockRawResultFactory = $this->getMockBuilder( ResultFactory::class )
		                             ->setMethods( [ 'create' ] )
		                             ->disableOriginalConstructor()
		                             ->getMock();

		// Set our expectation (when we call ResultFactory::create(ResultFactory::TYPE_RAW) we expect to get a RawResult object back)
		$mockRawResultFactory->method( 'create' )->with( ResultFactory::TYPE_RAW )->willReturn( $this->_mockRawResult );

		// Mock the ActionContext object. The following two methods of doing so are equivalent.
		/** @var ActionContext|\PHPUnit_Framework_MockObject_MockObject $mockContext */
//		$mockContext = $this->getMock( ActionContext::class, [], [], '', false );
		$mockContext = $this->getMockBuilder( ActionContext::class )
		                    ->disableOriginalConstructor()
		                    ->getMock();

		// Mock the request
		$this->_mockRequest = $this->getMockBuilder( Request::class )
		                           ->disableOriginalConstructor()
		                           ->getMock();

		$mockContext->method( 'getRequest' )->willReturn( $this->_mockRequest );

		$this->_mockUseCase = $this->getMockBuilder( UseCase::class )
		                           ->setMethods( ['processData'] )
		                           ->disableOriginalConstructor()
		                           ->getMock();

		$this->controller = new Index( $mockContext, $mockRawResultFactory, $this->_mockUseCase );
	}

	public function testReturnsResultInstance()
	{
		$this->_mockRequest->method( 'getMethod' )->willReturn( 'POST' );
		$this->assertInstanceOf( ResultInterface::class, $this->controller->execute() );
	}

	public function testReturns405MethodNotAllowedForNonPostRequests()
	{
		$this->_mockRequest->method( 'getMethod' )->willReturn( 'GET' );
		$this->_mockRawResult->expects( $this->once() )->method( 'setHttpResponseCode' )->with( 405 );
		$this->controller->execute();
	}

	public function testReturns400BadRequestIfRequiredArgumentsAreMissing()
	{
		$incompleteArguments = [];
		$this->_mockRequest->method( 'getMethod' )->willReturn( 'POST' );
		$this->_mockRequest->method( 'getParams' )->willReturn( $incompleteArguments );

		$this->_mockUseCase->expects( $this->once() )->method( 'processData' )->with( $incompleteArguments )->willThrowException( new RequiredArgumentMissingException( 'Test Exception: Required argument missing' ));

		$this->_mockRawResult->expects( $this->once() )->method( 'setHttpResponseCode' )->with( 400 );

		$this->controller->execute();
	}

}
