<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Model;

use Magento\Store\Model\StoreManagerInterface;

class GetStoresFromSwitch
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $websiteId
     * @param string $storeGroupId
     * @param string $storeId
     * @return string[]
     */
    public function execute(string $storeId, string $websiteId = '', string $storeGroupId = ''): array
    {
        if ($storeId) {
            return [$storeId];
        }

        $stores = $this->storeManager->getStores(true);

        $result = [];
        foreach ($stores as $store) {
            if ($storeGroupId) {
                if ($store->getStoreGroupId() !== $storeGroupId) {
                    continue;
                }
                $result[] = $store->getId();
            } elseif ($websiteId) {
                if ($store->getWebsiteId() !== $websiteId) {
                    continue;
                }
                $result[] = $store->getId();
            } else {
                if ($store->getWebsiteId() !== '0') {
                    continue;
                }
                $result[] = $store->getId();
            }
        }
        return $result;
    }
}
