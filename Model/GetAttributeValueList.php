<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\ObjectManagerInterface;
use Websolute\AttributeValueExtra\Model\ResourceModel\GetAttributeOptionIdsByAttributeId;
use Websolute\AttributeValueExtra\Model\ResourceModel\GetAttributeOptionValueByOptionId;

class GetAttributeValueList
{
    private $attributeFactory;

    /**
     * @var Attribute
     */
    private $attribute;

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
    private $getAttributeOptionValueByOptionId;

    /**
     * @var GetStoresFromSwitch
     */
    private $getStoresFromSwitch;

    public function __construct(
        AttributeFactory $attributeFactory,
        Attribute $attribute,
        ObjectManagerInterface $objectManager,
        GetAttributeOptionIdsByAttributeId $getAttributeOptionIdsByAttributeId,
        GetAttributeOptionValueByOptionId $getAttributeOptionValueByOptionId,
        GetStoresFromSwitch $getStoresFromSwitch
    )
    {
        $this->attributeFactory = $attributeFactory;
        $this->attribute = $attribute;
        $this->objectManager = $objectManager;
        $this->getAttributeOptionIdsByAttributeId = $getAttributeOptionIdsByAttributeId;
        $this->getAttributeOptionValueByOptionId = $getAttributeOptionValueByOptionId;
        $this->getStoresFromSwitch = $getStoresFromSwitch;
    }

    public function execute(string $attributeId, string $websiteId, string $storeGroupId, string $storeId): array
    {
        $attribute = $this->attributeFactory->create();
        $this->attribute->load($attribute, $attributeId, 'attribute_id');

        $attribute = $attribute->getData();

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
            $options = $this->getAttributeOptionValueByOptionId->execute($optionIds, [0]);
            foreach ($options as $option) {
                $values[] = [
                    'value_id' => $option['value_id'],
                    'option_id' => $option['option_id'],
                    'label' => $option['value'],
                    'store_id' => $option['store_id']
                ];
            }
        }
        return $values;
    }
}
