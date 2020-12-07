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
    private GetEntityTypeIdByCode $getEntityTypeIdByCode;

    /**
     * @var Attribute
     */
    private Attribute $attribute;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

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
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductExtraByAttributeCodeValueId(string $attributeCode, string $valueId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $entityTypeId = $this->getEntityTypeIdByCode->execute(Product::ENTITY);
        $attributeId = $this->attribute->getIdByCode(Product::ENTITY, $attributeCode);

        $data = [
            'entity_type_id' => $entityTypeId,
            'attribute_id' => $attributeId,
            'value_id' => $valueId,
            'store_id' => $storeId
        ];

        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('*')
            ->addFieldToFilter($data);

        $item = $collection->getFirstItem();

        if (!$item) {
            $data['store_id'] = '0';
            $collection = $this->collectionFactory->create();
            $collection->addFieldToSelect('*')
                ->addFieldToFilter($data);
            $item = $collection->getFirstItem();
        }

        return $item;
    }
}
