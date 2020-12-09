<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Websolute\AttributeValueExtra\Model\AttributeValueExtra;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Websolute\AttributeValueExtra\Model\AttributeValueExtraManager;
use Websolute\AttributeValueExtra\Model\ImageUpload;
use Websolute\AttributeValueExtra\Model\ResourceModel\AttributeValueExtra\CollectionFactory;
use Websolute\AttributeValueExtra\Model\ResourceModel\AttributeValueExtra\Collection;

class DataProvider extends AbstractDataProvider
{
    protected $_loadedData;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ImageUpload
     */
    private $imageUpload;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var AttributeValueExtraManager
     */
    private $attributeValueExtraManager;

    /**
     * @param AttributeValueExtraManager $attributeValueExtraManager
     * @param RequestInterface $request
     * @param CollectionFactory $contactCollectionFactory
     * @param ImageUpload $imageUpload
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        AttributeValueExtraManager $attributeValueExtraManager,
        RequestInterface $request,
        CollectionFactory $contactCollectionFactory,
        ImageUpload $imageUpload,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $contactCollectionFactory->create();
        $this->imageUpload = $imageUpload;
        $this->request = $request;
        $this->attributeValueExtraManager = $attributeValueExtraManager;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getData(): array
    {
        // @todo if extra already exists, then load it. Trying to create a new extra, with existing match, will fail for contraint instead of load it end edit it
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $items = $this->collection->getItems();

        if (count($items) === 0) {
            $params = $this->request->getParams();
            $params = $this->parseItemArray($params);

            if (isset($params['key'])) {
                unset($params['key']);
            }

            $this->_loadedData[$params['row_id']] = $params;
        } else {
            foreach ($items as $item) {
                $item = $this->parseItem($item);
                $data = $item->getData();
                if (isset($data['image']) && $data['image'] !== '') {
                    $imageArr = [];
                    $imageArr[0]['name'] = 'Image';
                    $imageArr[0]['type'] = 'image';
                    $imageArr[0]['url'] = $this->imageUpload->getFinalFilePath($data['image']);
                    $data['image'] = $imageArr;
                }

                $this->_loadedData[$item->getId()] = $data;
            }
        }

        return $this->_loadedData;
    }

    /**
     * @param $item
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function parseItem($item)
    {
        $attributeId = $item->getData('attribute_id');
        $attributeCode = '';

        if ($attributeId) {
            $attributeCode = $this->attributeValueExtraManager->getAttributeCodeById((string)$attributeId);
            $item->setData('attribute_code', $attributeCode);
        }

        $entityTypeId = $item->getData('entity_type_id');
        $entityTypeCode = '';

        if ($entityTypeId) {
            $entityTypeCode = $this->attributeValueExtraManager->getEntityTypeCodeById((string)$entityTypeId);
            $item->setData('entity_type_code', $entityTypeCode);
        }

        $storeId = $item->getData('store_id');

        if ($storeId !== null && $storeId !== '') {
            $storeName = $this->attributeValueExtraManager->getStoreNameById((string)$storeId);
            $item->setData('store_name', $storeName);
        }

        $optionId = $item->getData('option_id');

        if ($optionId !== null && $optionId !== '' &&
            $entityTypeCode !== null && $entityTypeCode !== '' &&
            $attributeCode !== null && $attributeCode !== ''
        ) {
            $valueLabel = $this->attributeValueExtraManager->getOptionValueCodeByoptionIdValueId(
                (string)$entityTypeCode,
                (string)$attributeCode,
                (string)$optionId
            );
            $item->setData('value_label', $valueLabel);
        }

        return $item;
    }

    /**
     * @param $item
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function parseItemArray($item)
    {
        $attributeId = $item['attribute_id'];
        $attributeCode = '';

        if ($attributeId) {
            $attributeCode = $this->attributeValueExtraManager->getAttributeCodeById((string)$attributeId);
            $item['attribute_code'] = $attributeCode;
        }

        $entityTypeId = $item['entity_type_id'];
        $entityTypeCode = '';

        if ($entityTypeId) {
            $entityTypeCode = $this->attributeValueExtraManager->getEntityTypeCodeById((string)$entityTypeId);
            $item['entity_type_code']= $entityTypeCode;
        }

        $storeId = $item['store_id'];

        if ($storeId !== null && $storeId !== '') {
            $storeName = $this->attributeValueExtraManager->getStoreNameById((string)$storeId);
            $item['store_name'] = $storeName;
        }

        $optionId = $item['option_id'];

        if ($optionId !== null && $optionId !== '' &&
            $entityTypeCode !== null && $entityTypeCode !== '' &&
            $attributeCode !== null && $attributeCode !== ''
        ) {
            $valueLabel = $this->attributeValueExtraManager->getOptionValueCodeByoptionIdValueId(
                (string)$entityTypeCode,
                (string)$attributeCode,
                (string)$optionId
            );
            $item['value_label'] = $valueLabel;
        }

        return $item;
    }
}
