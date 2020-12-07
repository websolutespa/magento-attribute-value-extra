<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class GetEntityTypeIdByCode
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
     * @param string $entityCode
     * @return int
     */
    public function execute(string $entityCode): int
    {
        $connection = $this->resourceConnection->getConnection();

        $tableName = $connection->getTableName('eav_entity_type');

        $qry = $connection->select()
            ->from($tableName, ['entity_type_id'])
            ->where('entity_type_code=?', $entityCode);

        return (int)$connection->fetchAll($qry)[0]['entity_type_id'];
    }
}
