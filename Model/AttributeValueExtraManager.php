<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class AttributeValueExtraManager
{
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $attributeId
     * @return string
     * @throws LocalizedException
     */
    public function getAttributeCodeById(string $attributeId): string
    {
        $attributeCollection = $this->attributeCollectionFactory->create();
        $attributeCollection->addFieldToFilter('attribute_id', $attributeId);
        $attribute = $attributeCollection->getFirstItem();
        if (!$attribute->getId()) {
            throw new LocalizedException(__('Attribute with id ? not found', $attributeId));
        }
        return $attribute->getAttributeCode();
    }

    /**
     * @param string $entityTypeId
     * @return string
     * @throws LocalizedException
     */
    public function getEntityTypeCodeById(string $entityTypeId): string
    {
        $entityType = $this->eavConfig->getEntityType($entityTypeId);
        if (!$entityType->getId()) {
            throw new LocalizedException(__('Entity Type with id ? not found', $entityTypeId));
        }
        return $entityType->getEntityTypeCode();
    }

    /**
     * @param string $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getStoreNameById(string $storeId): string
    {
        $store = $this->storeManager->getStore($storeId);
        return $store->getName();
    }

    /**
     * @param string $entityType
     * @param string $attributeCode
     * @param string $optionId
     * @return string
     * @throws LocalizedException
     */
    public function getOptionValueCodeByoptionIdValueId(
        string $entityType,
        string $attributeCode,
        string $optionId
    ): string {
        $this->storeManager->setCurrentStore('admin');
        $attribute = $this->eavConfig->getAttribute($entityType, $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if ($option['label'] && $optionId === $option['value']) {
                return $option['label'];
            }
        }
        return '';
    }
}
