<?php

namespace Mage2Kata\ActionController\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\ForwardFactory;

class Index extends Action
{
	/**
	 * @var PageFactory
	 */
	private $pageFactory;
	/**
	 * @var ForwardFactory
	 */
	private $forwardFactory;

	public function __construct( Context $context, PageFactory $pageFactory, ForwardFactory $forwardFactory)
	{

		parent::__construct( $context );
		$this->pageFactory = $pageFactory;
		$this->forwardFactory = $forwardFactory;
	}

	public function execute()
	{
		return $this->isGetRequest() ? $this->handleGetRequest() : $this->handleNonGetRequest();
	}

	/**
	 * @return \Magento\Framework\App\Request\Http
	 */
	public function getRequest()
	{
		return parent::getRequest();
	}

	/**
	 * @return \Magento\Framework\Controller\Result\Forward
	 */
	protected function handleNonGetRequest(): \Magento\Framework\Controller\Result\Forward
	{
		$forward = $this->forwardFactory->create();
		$forward->forward( 'noroute' );

		return $forward;
	}

	/**
	 * @return \Magento\Framework\View\Result\Page
	 */
	protected function handleGetRequest(): \Magento\Framework\View\Result\Page
	{
		return $this->pageFactory->create();
	}

	/**
	 * @return bool
	 */
	protected function isGetRequest(): bool
	{
		return $this->getRequest()->getMethod() === 'GET';
	}
}
