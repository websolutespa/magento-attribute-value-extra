<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model\ResourceModel\Product;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class GetTextAttributeValues
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
     * @param string $attributeId
     * @return array
     */
    public function execute(string $attributeId): array
    {
        $connection = $this->resourceConnection->getConnection();

        $tableName = $connection->getTableName('catalog_product_entity_text');

        $where = $this->getWhere($connection, $attributeId);

        $qry = $connection->select()
            ->from($tableName)
            ->where($where);

        return $connection->fetchAll($qry);
    }

    /**
     * @param string $attributeId
     * @param AdapterInterface $connection
     * @return string
     */
    private function getWhere(
        AdapterInterface $connection,
        string $attributeId
    ): string {
        return $connection->quoteInto(
            'attribute_id = ?',
            $attributeId
        );
    }
}
