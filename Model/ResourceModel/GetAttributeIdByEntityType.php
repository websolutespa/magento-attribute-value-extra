<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class GetAttributeIdByEntityType
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
     * @param string $entityTypeId
     * @param string $attributeType
     * @return array
     */
    public function execute(string $entityTypeId, string $attributeType): array
    {
        $connection = $this->resourceConnection->getConnection();

        $tableName = $connection->getTableName('eav_attribute');

        $where = $this->getWhere($attributeType, $connection, $entityTypeId);

        $qry = $connection->select()
            ->from($tableName, [
                    'attribute_id',
                    'attribute_code',
                    'backend_type',
                    'source_model',
                    'frontend_input'
                ])
            ->where($where);

        return $connection->fetchAll($qry);
    }

    /**
     * @param string $attributeType
     * @param AdapterInterface $connection
     * @param string $entityTypeId
     * @return string
     */
    private function getWhere(
        string $attributeType,
        AdapterInterface $connection,
        string $entityTypeId
    ): string {
        if (strpos($attributeType, 'select') !== false) {
            $where = 'backend_type not in ("static") and frontend_input LIKE "%select"'
                 . $connection->quoteInto(
                     'and entity_type_id=?',
                     $entityTypeId
                 );
        } else {
            $where = $connection->quoteInto(
                'backend_type not in ("static") and frontend_input = ?',
                $attributeType
            ) . $connection->quoteInto(
                'and entity_type_id=?',
                $entityTypeId
            );
        }
        return $where;
    }
}
