<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\ViewModel;

use Magento\Backend\Model\Url;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Websolute\AttributeValueExtra\Model\Config;
use Websolute\AttributeValueExtra\Model\GetListAttributeValueExtra;

class AttributeValueExtraText implements ArgumentInterface
{
    private $attributeType = 'text';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var GetListAttributeValueExtra
     */
    private $getListAttributeValueExtra;

    /**
     * @var Url
     */
    private $backendUrl;

    /**
     * @param Config $config
     * @param GetListAttributeValueExtra $getListAttributeValueExtra
     * @param Url $backendUrl
     */
    public function __construct(
        Config $config,
        GetListAttributeValueExtra $getListAttributeValueExtra,
        Url $backendUrl
    ) {
        $this->config = $config;
        $this->getListAttributeValueExtra = $getListAttributeValueExtra;
        $this->backendUrl = $backendUrl;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getListAttributeValueExtra(): array
    {
        return $this->getListAttributeValueExtra->execute($this->attributeType);
    }

    /**
     * @param $row
     * @return string
     */
    public function generateUrlBackend($row): string
    {
        return $this->backendUrl->getUrl('*/*/createtext', [
            'row_id' => $row['row_id'],
            'store_id' => $row['store_id'],
            'entity_type_id' => $row['entity_type_id'],
            'attribute_id' => $row['attribute_id']
        ]);
    }

    /**
     * @param $key
     * @return string
     */
    public function getLinkTitle($key): string
    {
        return $this->backendUrl->getUrl('*/*/createtext', ['key' => $key]);
    }
}
