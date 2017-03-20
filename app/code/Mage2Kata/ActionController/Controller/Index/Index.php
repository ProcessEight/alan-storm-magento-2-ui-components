<?php
namespace Mage2Kata\ActionController\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
	/** @var \Magento\Framework\Controller\Result\Raw */
	protected $result;

	/** @var \Magento\Framework\Controller\ResultFactory */
	protected $resultFactory;

	public function __construct(
		Context $context,
		ResultFactory $resultFactory
	)
	{
		parent::__construct( $context );
		$this->resultFactory = $resultFactory;
	}

	/**
	 * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
	 */
	protected function getMethodNotAllowedResult()
	{
		$this->result = $this->resultFactory->create( ResultFactory::TYPE_RAW );
		$this->result->setHttpResponseCode( 405 );

		return $this->result;
	}

	public function execute()
	{
		return $this->getMethodNotAllowedResult();
	}
}
