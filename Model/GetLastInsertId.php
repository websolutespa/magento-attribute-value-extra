<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use Websolute\AttributeValueExtra\Model\ResourceModel\AttributeValueExtra\CollectionFactory;

class GetLastInsertId
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
     * @return string
     */
    public function execute(): string
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('row_id')
            ->setOrder('row_id');

        return (string)$collection->getFirstItem()->getData('row_id');
    }
}
