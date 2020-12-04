<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

class GetOptionIdByLabel
{
    /**
     * @param $product
     * @param string $attributeCode
     * @param string $optionLabel
     * @return string
     */
    public function execute($product, string $attributeCode, string $optionLabel): string
    {
        $isAttributeExist = $product->getResource()->getAttribute($attributeCode);
        $optionId = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionId = $isAttributeExist->getSource()->getOptionId($optionLabel);
        }
        return $optionId;
    }
}
