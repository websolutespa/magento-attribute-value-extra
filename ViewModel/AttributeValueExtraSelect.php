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

class AttributeValueExtraSelect implements ArgumentInterface
{
    private $attributeType = 'select';

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
    private \Magento\Backend\Block\Store\Switcher $switcher;

    /**
     * @param Config $config
     * @param \Magento\Backend\Block\Store\Switcher $switcher
     * @param GetListAttributeValueExtra $getListAttributeValueExtra
     * @param Url $backendUrl
     */
    public function __construct(
        Config $config,
        \Magento\Backend\Block\Store\Switcher $switcher,
        GetListAttributeValueExtra $getListAttributeValueExtra,
        Url $backendUrl
    ) {
        $this->config = $config;
        $this->getListAttributeValueExtra = $getListAttributeValueExtra;
        $this->backendUrl = $backendUrl;
        $this->switcher = $switcher;
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
        return $this->backendUrl->getUrl('*/*/create', [
            'row_id' => $row['row_id'],
            'store_id' => $row['store_id'],
            'entity_type_id' => $row['entity_type_id'],
            'option_id' => $row['option_id'],
            'attribute_id' => $row['attribute_id'],
            'value_id' => $row['value_id']
        ]);
    }

    /**
     * @param $key
     * @return string
     */
    public function getLinkTitle($key): string
    {
        return $this->backendUrl->getUrl('*/*/create', ['key' => $key]);
    }

    public function getEntityTypeCodes (): array
    {
        return [
            [
                'name' => 'catalog_product',
                'value' => 4
            ]
        ];
    }

    /**
     * @return string
     */
    public function getSwitcherWebsite (): string
    {
        return (string)$this->switcher->getWebsiteId() ?: '';
    }

    /**
     * @return string
     */
    public function getSwitcherStoreGroup (): string
    {
        return (string)$this->switcher->getStoreGroupId() ?: '';
    }

    /**
     * @return string
     */
    public function getSwitcherStore (): string
    {
        return (string)$this->switcher->getStoreId() ?: '';
    }
}
