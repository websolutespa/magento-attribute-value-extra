<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Controller\Adminhtml\AttributeValueExtra;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Createtext extends Action
{
    const ADMIN_RESOURCE = 'Websolute_AttributeValueExtra::attributevalueextra_resource';

    protected $resultPageFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Websolute_AttributeValueExtra::menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Attribute Extra Text'));
        return $resultPage;
    }
}
