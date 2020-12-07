<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class GetAttributeOptionValueByOptionId
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $optionIds
     * @param int[] $storeIds
     * @return array
     */
    public function execute(array $optionIds, array $storeIds): array
    {
        if (!count($storeIds)) {
            $storeIds = [0];
        }
        $connection = $this->resourceConnection->getConnection();

        $tableName = $connection->getTableName('eav_attribute_option_value');

        $qry = $connection->select()
            ->from($tableName)
            ->where('option_id in (?)', $optionIds)
            ->where('store_id in (?)', $storeIds);

        return $connection->fetchAll($qry);
    }
}
