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
use Magento\Backend\Model\Session\Proxy as Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Websolute\AttributeValueExtra\Model\AttributeValueExtraFactory;
use Websolute\AttributeValueExtra\Model\ImageUpload;
use Websolute\AttributeValueExtra\Model\ResourceModel\AttributeValueExtra;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Websolute_AttributeValueExtra::attributevalueextra_resource';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var AttributeValueExtraFactory
     */
    protected $attributeValueExtraFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;

    /**
     * @var ImageUpload
     */
    private $imageUpload;

    /**
     * @var AttributeValueExtra
     */
    private $attributeValueExtra;

    /**
     * @param Context $context
     * @param \Magento\Backend\Model\Session $session
     * @param PageFactory $resultPageFactory
     * @param ImageUpload $imageUpload
     * @param AttributeValueExtra $attributeValueExtra
     * @param AttributeValueExtraFactory $attributeValueExtraFactory
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Model\Session $session,
        PageFactory $resultPageFactory,
        ImageUpload $imageUpload,
        AttributeValueExtra $attributeValueExtra,
        AttributeValueExtraFactory $attributeValueExtraFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->attributeValueExtraFactory = $attributeValueExtraFactory;
        $this->session = $session;
        $this->imageUpload = $imageUpload;
        $this->attributeValueExtra = $attributeValueExtra;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            try {
                $id = $data['row_id'];
                $entity = $this->attributeValueExtraFactory->create();
                $this->attributeValueExtra->load($entity, $id);
                $data = array_filter($data, function ($value) {
                    return $value !== '';
                });

                if (isset($data['image']) && count($data['image'])) {
                    $data['image'] = $data['image'][0]['name'] ?: null;
                    if ($data['image']) {
                        $this->imageUpload->moveFileFromTmp($data['image']);
                    }
                } else {
                    $data['image'] = '';
                }
                if (!$entity->getId()) {
                    unset($data['row_id']);
                }
                $entity->setData($data);
                $this->attributeValueExtra->save($entity);
                $this->messageManager->addSuccessMessage(__('Successfully saved the item.'));
                $this->session->setFormData(false);
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->session->setFormData($data);
                return $resultRedirect->setPath('*/*/', ['id' => $entity->getId()]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
