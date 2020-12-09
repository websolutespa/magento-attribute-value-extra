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
use Magento\Framework\Exception\LocalizedException;
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
    )
    {
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
     * @throws LocalizedException
     */
    public function getProductExtraByAttributeCodeOptionId(string $attributeCode, string $optionId): DataObject
    {
        $entityTypeId = $this->getEntityTypeIdByCode->execute(Product::ENTITY);
        $attributeId = (int)$this->attribute->getIdByCode(Product::ENTITY, $attributeCode);

        $item = $this->getByStore($entityTypeId, $attributeId, 'option_id', $optionId);

        if ($item->getId()) {
            return $item;
        }

        $item = $this->getByStoreGroup($entityTypeId, $attributeId, 'option_id', $optionId);

        if ($item->getId()) {
            return $item;
        }

        $item = $this->getByWebsite($entityTypeId, $attributeId, 'option_id', $optionId);

        if ($item->getId()) {
            return $item;
        }

        $item = $this->getByAllStoreView($entityTypeId, $attributeId, 'option_id', $optionId);

        return $item;
    }

    /**
     * @param string $attributeCode
     * @param string $valueId
     * @return DataObject
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getProductExtraByAttributeCodeValueId(string $attributeCode, string $valueId): DataObject
    {
        $entityTypeId = $this->getEntityTypeIdByCode->execute(Product::ENTITY);
        $attributeId = (int)$this->attribute->getIdByCode(Product::ENTITY, $attributeCode);

        $item = $this->getByStore($entityTypeId, $attributeId, 'value_id', $valueId);

        if ($item->getId()) {
            return $item;
        }

        $item = $this->getByStoreGroup($entityTypeId, $attributeId, 'value_id', $valueId);

        if ($item->getId()) {
            return $item;
        }

        $item = $this->getByWebsite($entityTypeId, $attributeId, 'value_id', $valueId);

        if ($item->getId()) {
            return $item;
        }

        $item = $this->getByAllStoreView($entityTypeId, $attributeId, 'value_id', $valueId);

        return $item;
    }

    /**
     * @param int $entityTypeId
     * @param int $attributeId
     * @param string $valueId
     * @return DataObject
     * @throws NoSuchEntityException
     */
    private function getByStore(int $entityTypeId, int $attributeId, string $fieldName, string $fieldValue): DataObject
    {
        $storeId = $this->storeManager->getStore()->getId();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*');
        $collection->addFieldToFilter('entity_type_id', $entityTypeId);
        $collection->addFieldToFilter('attribute_id', $attributeId);
        $collection->addFieldToFilter($fieldName, $fieldValue);
        $collection->addFieldToFilter('store_id', $storeId);

        return $collection->getFirstItem();
    }

    /**
     * @param int $entityTypeId
     * @param int $attributeId
     * @param string $valueId
     * @return DataObject
     */
    private function getByStoreGroup(int $entityTypeId, int $attributeId, string $fieldName, string $fieldValue): DataObject
    {
        $storeGroupId = $this->storeManager->getGroup()->getId();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*');
        $collection->addFieldToFilter('entity_type_id', $entityTypeId);
        $collection->addFieldToFilter('attribute_id', $attributeId);
        $collection->addFieldToFilter($fieldName, $fieldValue);
        $collection->addFieldToFilter('store_group_id', $storeGroupId);

        return $collection->getFirstItem();
    }

    /**
     * @param int $entityTypeId
     * @param int $attributeId
     * @param string $valueId
     * @return DataObject
     * @throws LocalizedException
     */
    private function getByWebsite(int $entityTypeId, int $attributeId, string $fieldName, string $fieldValue): DataObject
    {
        $websiteId = $this->storeManager->getWebsite()->getId();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*');
        $collection->addFieldToFilter('entity_type_id', $entityTypeId);
        $collection->addFieldToFilter('attribute_id', $attributeId);
        $collection->addFieldToFilter($fieldName, $fieldValue);
        $collection->addFieldToFilter('website_id', $websiteId);

        return $collection->getFirstItem();
    }

    /**
     * @param int $entityTypeId
     * @param int $attributeId
     * @param string $valueId
     * @return DataObject
     */
    private function getByAllStoreView(int $entityTypeId, int $attributeId, string $fieldName, string $fieldValue): DataObject
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*');
        $collection->addFieldToFilter('entity_type_id', $entityTypeId);
        $collection->addFieldToFilter('attribute_id', $attributeId);
        $collection->addFieldToFilter($fieldName, $fieldValue);
        $collection->addFieldToFilter('store_id', '0');
        $collection->addFieldToFilter('store_group_id', '0');
        $collection->addFieldToFilter('website_id', '0');

        return $collection->getFirstItem();
    }
}
