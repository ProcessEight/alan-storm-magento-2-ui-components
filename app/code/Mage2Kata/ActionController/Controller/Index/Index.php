<?php
namespace Mage2Kata\ActionController\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
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

	public function execute()
	{
		return $this->resultFactory->create( ResultFactory::TYPE_RAW );
	}
}
