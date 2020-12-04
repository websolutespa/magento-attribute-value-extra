<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Block\Adminhtml\AttributeValueExtra\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface\Proxy as UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * @param UrlInterface $urlInterface
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        Context $context,
        Registry $registry
    ) {
        $this->urlInterface = $urlInterface;
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label' => __('Delete'),
            'on_click' =>
                'deleteConfirm(\'' . __('Are you sure you want to delete this row ?') . '\', \'' .
                $this->getDeleteUrl() .
                '\')',
            'class' => 'delete',
            'sort_order' => 20
        ];
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        $url = $this->urlInterface->getCurrentUrl();
        $parts = explode('/', parse_url($url, PHP_URL_PATH));
        $id = $parts[6];
        return $this->getUrl('*/*/delete', ['id' => $id]);
    }
}
