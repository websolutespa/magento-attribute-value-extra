<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Websolute\AttributeValueExtra\Model\ResourceModel\GetAttributeIdByEntityType;
use Websolute\AttributeValueExtra\Model\ResourceModel\GetAttributeOptionIdsByAttributeId;
use Websolute\AttributeValueExtra\Model\ResourceModel\GetAttributeOptionValueByOptionId;
use Websolute\AttributeValueExtra\Model\ResourceModel\GetEntityTypeIdByCode;
use Websolute\AttributeValueExtra\Model\ResourceModel\Product\GetTextAttributeValues;

class GetListAttributeValueExtra
{
    public $availbleEntityCodes = [
        Product::ENTITY,
        Category::ENTITY,
        Customer::ENTITY
    ];

    /**
     * @var GetEntityTypeIdByCode
     */
    private $getEntityTypeIdByCode;

    /**
     * @var GetAttributeIdByEntityType
     */
    private $attributeIdByEntityType;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var GetAttributeOptionIdsByAttributeId
     */
    private $getAttributeOptionIdsByAttributeId;

    /**
     * @var GetAttributeOptionValueByOptionId
     */
    private $optionValueByOptionId;

    /**
     * @var GetLastInsertId
     */
    private $getLastIncrementId;

    /**
     * @var ResourceModel\Product\GetTextAttributeValues
     */
    private $getTextAttributeValues;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param GetEntityTypeIdByCode $getEntityTypeIdByCode
     * @param GetAttributeOptionValueByOptionId $optionValueByOptionId
     * @param GetAttributeOptionIdsByAttributeId $getAttributeOptionIdsByAttributeId
     * @param ObjectManagerInterface $objectManager
     * @param GetLastInsertId $getLastIncrementId
     * @param GetAttributeIdByEntityType $attributeIdByEntityType
     * @param StoreManagerInterface $storeManager
     * @param ResourceModel\Product\GetTextAttributeValues $getTextAttributeValues
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        GetEntityTypeIdByCode $getEntityTypeIdByCode,
        GetAttributeOptionValueByOptionId $optionValueByOptionId,
        GetAttributeOptionIdsByAttributeId $getAttributeOptionIdsByAttributeId,
        ObjectManagerInterface $objectManager,
        GetLastInsertId $getLastIncrementId,
        GetAttributeIdByEntityType $attributeIdByEntityType,
        StoreManagerInterface $storeManager,
        GetTextAttributeValues $getTextAttributeValues
    ) {
        $this->getEntityTypeIdByCode = $getEntityTypeIdByCode;
        $this->attributeIdByEntityType = $attributeIdByEntityType;
        $this->eavConfig = $eavConfig;
        $this->objectManager = $objectManager;
        $this->getAttributeOptionIdsByAttributeId = $getAttributeOptionIdsByAttributeId;
        $this->optionValueByOptionId = $optionValueByOptionId;
        $this->getLastIncrementId = $getLastIncrementId;
        $this->getTextAttributeValues = $getTextAttributeValues;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $attributeType
     * @return array
     * @throws NoSuchEntityException
     */
    public function execute(string $attributeType): array
    {
        $result = [];

        if ($attributeType === '') {
            return $result;
        }

        foreach ($this->availbleEntityCodes as $entityCode) {
            $entityValues = $this->getEntityTypeIdByCode->execute((string)$entityCode);
            $entityTypeId = key($entityValues);
            $attributes = $this->attributeIdByEntityType->execute((string)$entityTypeId, $attributeType);
            foreach ($attributes as $attribute) {
                if (strpos($attributeType, 'select') !== false) {
                    $result = $this->parseSelectAttribute($attribute, $entityTypeId, $entityCode, $result);
                } elseif (strpos($attributeType, 'text') !== false) {
                    $result = $this->parseTextAttribute($attribute, $entityTypeId, $entityCode, $result);
                }
            }
        }
        return $result;
    }

    /**
     * @param $attribute
     * @param int|null $entityTypeId
     * @param $entityCode
     * @param array $result
     * @return array
     * @throws NoSuchEntityException
     */
    protected function parseSelectAttribute($attribute, ?int $entityTypeId, $entityCode, array $result): array
    {
        $values = [];
        if (
            $attribute['source_model'] &&
            $attribute['source_model'] !== Table::class
        ) {
            $sourceModel = $this->objectManager->create($attribute['source_model']);
            $optionIds = $sourceModel->getAllOptions();
            foreach ($optionIds as $option) {
                if (!$option['label'] || !$option['value']) {
                    continue;
                }
                $values[] = [
                    'value_id' => $option['value'],
                    'option_id' => $option['value'],
                    'label' => $option['label'],
                    'store_id' => '0'
                ];
            }
        } else {
            $optionIds = $this->getAttributeOptionIdsByAttributeId
                ->execute($attribute['attribute_id']);
            $options = $this->optionValueByOptionId->execute($optionIds);
            foreach ($options as $option) {
                $values[] = [
                    'value_id' => $option['value_id'],
                    'option_id' => $option['option_id'],
                    'label' => $option['value'],
                    'store_id' => $option['store_id']
                ];
            }
        }
        $lastRowId = (int)$this->getLastIncrementId->execute();
        foreach ($values as $value) {
            $storeCode = $this->storeManager->getStore($value['store_id'])->getName();
            $result[] = [
                'row_id' => $lastRowId + 1,
                'store_id' => $value['store_id'],
                'store_code' => $storeCode,
                'entity_type_id' => $entityTypeId,
                'entity_type_code' => $entityCode,
                'option_id' => $value['option_id'],
                'attribute_id' => $attribute['attribute_id'],
                'attribute_code' => $attribute['attribute_code'],
                'value_id' => $value['value_id'],
                'value_label' => $value['label'],
                'label' => 'store '
                    . $value['store_id'] . ' => '
                    . $entityCode . ' => '
                    . $attribute['attribute_code'] . ' => '
                    . $value['label']
            ];
        }
        return $result;
    }

    /**
     * @param $attribute
     * @param int|null $entityTypeId
     * @param $entityCode
     * @param array $result
     * @return array
     */
    protected function parseTextAttribute($attribute, ?int $entityTypeId, $entityCode, array $result): array
    {
        $stores = $this->storeManager->getStores();

        $lastRowId = (int)$this->getLastIncrementId->execute();
        foreach ($stores as $store) {
            $storeId = $store->getId();
            $result[] = [
                'row_id' => $lastRowId + 1,
                'store_id' => $storeId,
                'store_code' => $store->getName(),
                'entity_type_id' => $entityTypeId,
                'entity_type_code' => $entityCode,
                'attribute_id' => $attribute['attribute_id'],
                'attribute_code' => $attribute['attribute_code'],
                'label' => 'store '
                    . $storeId . ' => '
                    . $entityCode . ' => '
                    . $attribute['attribute_code']
            ];
        }
        return $result;
    }
}
