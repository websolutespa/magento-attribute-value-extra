<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class GetAttributeOptionIdsByAttributeId
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

        $tableName = $connection->getTableName('eav_attribute_option');

        $qry = $connection->select()
            ->from($tableName, ['option_id'])
            ->where('attribute_id=?', $attributeId);

        return $connection->fetchAll($qry);
    }
}
