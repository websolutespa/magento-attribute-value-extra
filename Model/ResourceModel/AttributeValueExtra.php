<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AttributeValueExtra extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(
            'websolute_attribute_value_extra',
            'row_id'
        );
    }
}
