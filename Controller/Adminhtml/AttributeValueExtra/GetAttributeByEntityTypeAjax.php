<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Controller\Adminhtml\AttributeValueExtra;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Websolute\AttributeValueExtra\Model\GetAttributesListByEntityType;

class GetAttributeByEntityTypeAjax extends Action
{
    const ADMIN_RESOURCE = 'Websolute_AttributeValueExtra::attributevalueextra_resource';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetAttributesListByEntityType
     */
    private $getAttributesListByEntityType;

    /**
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param GetAttributesListByEntityType $getAttributesListByEntityType
     * @param Context $context
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        RequestInterface $request,
        GetAttributesListByEntityType $getAttributesListByEntityType
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->getAttributesListByEntityType = $getAttributesListByEntityType;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $entityTypeId = $this->request->getParam('entity_type_id');

        $attributes = $this->getAttributesListByEntityType->execute((int)$entityTypeId);

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($attributes);
        return $resultJson;
    }
}
