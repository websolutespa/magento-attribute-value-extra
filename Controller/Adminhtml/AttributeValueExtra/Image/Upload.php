<?php
/*
 *  Copyright © Websolute spa. All rights reserved.
 *  See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\AttributeValueExtra\Controller\Adminhtml\AttributeValueExtra\Image;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Websolute\AttributeValueExtra\Model\ImageUpload;

/**
 * Class Upload
 */
class Upload extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Websolute_AttributeValueExtra::attributevalueextra_resource';

    /**
     * @var ImageUpload
     */
    protected $imageUpload;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param ImageUpload $imageUpload
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        ImageUpload $imageUpload
    ) {
        parent::__construct($context);
        $this->imageUpload = $imageUpload;
        $this->request = $request;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $params = $this->request->getParams();
            foreach ($params as $key => $param) {
                if ($key === 'param_name') {
                    break;
                }
            }

            $result = $this->imageUpload->saveFileToTmpDir($param);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
