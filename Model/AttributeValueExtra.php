<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Websolute\AttributeValueExtra\Api\Data\AttributeValueExtraInterface;

class AttributeValueExtra extends AbstractModel implements AttributeValueExtraInterface, IdentityInterface
{

    public const ROW_ID = 'row_id';

    public const ENTITY_TYPE_ID = 'entity_type_id';

    public const ATTRIBUTE_ID = 'attribute_id';

    public const VALUE_ID = 'value_id';

    public const STORE_ID = 'store_id';

    const CACHE_TAG = 'websolute_attributevalueextra_attributevalueextra';

    protected function _construct()
    {
        $this->_init(ResourceModel\AttributeValueExtra::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
