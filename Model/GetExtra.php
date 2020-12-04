<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use Magento\Framework\DataObject;
use Websolute\AttributeValueExtra\Model\ResourceModel\AttributeValueExtra\CollectionFactory;

class GetExtra
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $entityTypeId
     * @param string $attributeId
     * @param string $valueId
     * @param string $store_id
     * @return DataObject
     */
    public function execute(string $entityTypeId, string $attributeId, string $valueId, string $store_id): DataObject
    {
        $data = [
            'entity_type_id' => $entityTypeId,
            'attribute_id' => $attributeId,
            'value_id' => $valueId,
            'store_id' => $store_id
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
