<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class GetAttributesByEntityType
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
     * @param int $entityTypeId
     * @return array
     */
    public function execute(int $entityTypeId): array
    {
        $connection = $this->resourceConnection->getConnection();

        $tableName = $connection->getTableName('eav_attribute');

        $qry = $connection->select()
            ->from($tableName, [
                    'attribute_id',
                    'attribute_code'
                ])
            ->where('frontend_input like "%select%" AND entity_type_id = ?', $entityTypeId);

        return $connection->fetchAll($qry);
    }
}
