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
use Websolute\AttributeValueExtra\Model\GetAttributeValueList;

class GetAttributeValuesAjax extends Action
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
     * @var GetAttributeValueList
     */
    private $getAttributeValueList;

    /**
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param GetAttributesListByEntityType $getAttributesListByEntityType
     * @param Context $context
     */
    public function __construct(
        ResultFactory $resultFactory,
        RequestInterface $request,
        GetAttributesListByEntityType $getAttributesListByEntityType,
        GetAttributeValueList $getAttributeValueList,
        Context $context
    )
    {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->getAttributeValueList = $getAttributeValueList;
        $this->getAttributesListByEntityType = $getAttributesListByEntityType;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $params = $this->request->getParams();

        $attributeValueList = $this->getAttributeValueList->execute(
            $params['attribute_id'],
            $params['website_id'],
            $params['store_group_id'],
            $params['store_id']
        );

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($attributeValueList);
        return $resultJson;
    }
}
