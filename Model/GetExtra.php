<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Websolute\AttributeValueExtra\Model\ResourceModel\AttributeValueExtra\CollectionFactory;
use Websolute\AttributeValueExtra\Model\ResourceModel\GetEntityTypeIdByCode;

class GetExtra
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GetEntityTypeIdByCode
     */
    private $getEntityTypeIdByCode;

    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceModel\GetEntityTypeIdByCode $getEntityTypeIdByCode
     * @param StoreManagerInterface $storeManager
     * @param Attribute $attribute
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        GetEntityTypeIdByCode $getEntityTypeIdByCode,
        StoreManagerInterface $storeManager,
        Attribute $attribute,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->getEntityTypeIdByCode = $getEntityTypeIdByCode;
        $this->attribute = $attribute;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $attributeCode
     * @param string $valueId
     * @return DataObject
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductExtraByAttributeCodeValueId(string $attributeCode, string $valueId)
    {
        $entityTypeId = $this->getEntityTypeIdByCode->execute(Product::ENTITY);
        $attributeId = $this->attribute->getIdByCode(Product::ENTITY, $attributeCode);

        $item = $this->getByStore($entityTypeId, $attributeId, $valueId);

        if ($item) {
            return $item;
        }

        $item = $this->getByStoreGroup($entityTypeId, $attributeId, $valueId);

        if ($item) {
            return $item;
        }

        $item = $this->getByWebsite($entityTypeId, $attributeId, $valueId);

        if ($item) {
            return $item;
        }

        $item = $this->getByAllStoreView($entityTypeId, $attributeId, $valueId);

        return $item;
    }

    /**
     * @param array $entityTypeId
     * @param int $attributeId
     * @param string $valueId
     * @return DataObject
     * @throws NoSuchEntityException
     */
    private function getByStore (array $entityTypeId, int $attributeId, string $valueId): DataObject
    {
        $storeId = $this->storeManager->getStore()->getId();

        $data = [
            'entity_type_id' => $entityTypeId,
            'attribute_id' => $attributeId,
            'value_id' => $valueId,
            'store_id' => $storeId
        ];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*')
            ->addFieldToFilter($data);

        return $collection->getFirstItem()->getData();
    }

    /**
     * @param array $entityTypeId
     * @param int $attributeId
     * @param string $valueId
     * @return DataObject
     */
    private function getByStoreGroup (array $entityTypeId, int $attributeId, string $valueId): DataObject
    {
        $storeGroupId = $this->storeManager->getGroup()->getId();

        $data = [
            'entity_type_id' => $entityTypeId,
            'attribute_id' => $attributeId,
            'value_id' => $valueId,
            'store_group_id' => $storeGroupId
        ];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*')
            ->addFieldToFilter($data);

        return $collection->getFirstItem();
    }

    /**
     * @param array $entityTypeId
     * @param int $attributeId
     * @param string $valueId
     * @return DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getByWebsite (array $entityTypeId, int $attributeId, string $valueId): DataObject
    {
        $websiteId = $this->storeManager->getWebsite()->getId();

        $data = [
            'entity_type_id' => $entityTypeId,
            'attribute_id' => $attributeId,
            'value_id' => $valueId,
            'website_id' => $websiteId
        ];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*')
            ->addFieldToFilter($data);

        return $collection->getFirstItem();
    }

    /**
     * @param array $entityTypeId
     * @param int $attributeId
     * @param string $valueId
     * @return DataObject
     */
    private function getByAllStoreView (array $entityTypeId, int $attributeId, string $valueId): DataObject
    {
        $data = [
            'entity_type_id' => $entityTypeId,
            'attribute_id' => $attributeId,
            'value_id' => $valueId,
            'store_id' => '0',
            'store_group_id' => '0',
            'website_id' => '0'
        ];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*')
            ->addFieldToFilter($data);

        return $collection->getFirstItem();
    }
}
