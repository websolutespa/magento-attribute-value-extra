<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Controller\Adminhtml\AttributeValueExtra;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Websolute\AttributeValueExtra\Model\AttributeValueExtraFactory;

class Delete extends Action
{

    const ADMIN_RESOURCE = 'Websolute_AttributeValueExtra::attributevalueextra_resource';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var AttributeValueExtraFactory
     */
    protected $contactFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AttributeValueExtraFactory $contactFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AttributeValueExtraFactory $contactFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->contactFactory = $contactFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $contact = $this->contactFactory->create()->load($id);

        if (!$contact) {
            $this->messageManager->addError(__('Unable to process. please, try again.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/', ['_current' => true]);
        }

        try {
            $contact->delete();
            $this->messageManager->addSuccess(__('Row has been deleted !'));
        } catch (Exception $e) {
            $this->messageManager->addError(__('Error while trying to delete row'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', ['_current' => true]);
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
