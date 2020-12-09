<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use Websolute\AttributeValueExtra\Model\ResourceModel\GetAttributesByEntityType;

class GetAttributesListByEntityType
{
    /**
     * @var GetAttributesByEntityType
     */
    private $getAttributesByEntityType;

    /**
     * @param GetAttributesByEntityType $getAttributesByEntityType
     */
    public function __construct (
        GetAttributesByEntityType $getAttributesByEntityType
    ) {
        $this->getAttributesByEntityType = $getAttributesByEntityType;
    }

    /**
     * @param int $entityTypeId
     * @return array
     */
    public function execute (int $entityTypeId): array
    {
        return $this->getAttributesByEntityType->execute($entityTypeId);
    }
}
