<?php
namespace Mage2Kata\ActionController\Controller\Index;

use Mage2Kata\ActionController\Model\Exception\RequiredArgumentMissingException;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
	/** @var RedirectFactory */
	protected $_resultRedirectFactory;

	/** @var \Magento\Framework\Controller\Result\Raw */
	protected $_result;

	/** @var \Magento\Framework\Controller\ResultFactory */
	protected $resultFactory;

	/**
	 * @var UseCase
	 */
	private $useCase;

	public function __construct(
		Context $context,
		ResultFactory $resultFactory,
		UseCase $useCase
	)
	{
		parent::__construct( $context );
		$this->resultFactory = $resultFactory;
		$this->useCase       = $useCase;
		$this->_resultRedirectFactory = $context->getResultRedirectFactory();
	}

	/**
	 * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
	 */
	protected function _getMethodNotAllowedResult()
	{
		$this->_result = $this->resultFactory->create( ResultFactory::TYPE_RAW );
		$this->_result->setHttpResponseCode( 405 );

		return $this->_result;
	}

	public function execute()
	{
		return ! $this->isPostRequest() ? $this->_getMethodNotAllowedResult() : $this->processRequestAndRedirect();
	}

	protected function _getBadRequestResult()
	{
		$this->_result = $this->resultFactory->create( ResultFactory::TYPE_RAW );
		$this->_result->setHttpResponseCode( 400 );

		return $this->_result;
	}

	/**
	 * @return bool
	 */
	protected function isPostRequest(): bool
	{
		return ( $this->getRequest()->getMethod() === 'POST' );
	}

	/**
	 * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
	 */
	protected function processRequestAndRedirect()
	{
		try {
			$this->useCase->processData( $this->getRequest()->getParams() );

			$redirect = $this->_resultRedirectFactory->create();
			$redirect->setPath('/');
			return $redirect;

		} catch ( RequiredArgumentMissingException $exception ) {
			return $this->_getBadRequestResult();
		}
	}
}
