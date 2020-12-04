<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model\ResourceModel\AttributeValueExtra;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Websolute\AttributeValueExtra\Model\AttributeValueExtra as AttributeValueExtraModel;
use Websolute\AttributeValueExtra\Model\ResourceModel\AttributeValueExtra;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'row_id';

    protected function _construct()
    {
        $this->_init(
            AttributeValueExtraModel::class,
            AttributeValueExtra::class
        );
    }
}
