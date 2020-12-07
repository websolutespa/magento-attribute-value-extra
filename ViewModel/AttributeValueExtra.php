<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\ViewModel;

use Magento\Backend\Block\Store\Switcher;
use Magento\Backend\Model\Url;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Websolute\AttributeValueExtra\Model\Config;
use Websolute\AttributeValueExtra\Model\GetLastInsertId;
use Websolute\AttributeValueExtra\Model\GetListAttributeValueExtra;

class AttributeValueExtra implements ArgumentInterface
{
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
    private Switcher $switcher;
    private GetLastInsertId $getLastInsertId;

    /**
     * @param Config $config
     * @param Switcher $switcher
     * @param GetListAttributeValueExtra $getListAttributeValueExtra
     * @param Url $backendUrl
     * @param GetLastInsertId $getLastInsertId
     */
    public function __construct(
        Config $config,
        Switcher $switcher,
        GetListAttributeValueExtra $getListAttributeValueExtra,
        Url $backendUrl,
        GetLastInsertId $getLastInsertId
    ) {
        $this->config = $config;
        $this->getListAttributeValueExtra = $getListAttributeValueExtra;
        $this->backendUrl = $backendUrl;
        $this->switcher = $switcher;
        $this->getLastInsertId = $getLastInsertId;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return true;
    }

    public function getEntityTypeCodes(): array
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
    public function getSwitcherWebsite(): string
    {
        return (string)$this->switcher->getWebsiteId() ?: '';
    }

    /**
     * @return string
     */
    public function getSwitcherStoreGroup(): string
    {
        return (string)$this->switcher->getStoreGroupId() ?: '';
    }

    /**
     * @return string
     */
    public function getSwitcherStore(): string
    {
        return (string)$this->switcher->getStoreId() ?: '';
    }
}
