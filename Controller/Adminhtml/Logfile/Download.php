<?php
/**
 * LICENSE NOTICE
 *
 * This file is part of the Magezoid extension package.
 * Usage is governed by the Magezoid proprietary license agreement:
 * 
 *
 * DISCLAIMER
 *
 * Any modifications to this file may be overwritten during future upgrades.
 * Please extend or override functionality via Magento's customization standards.
 *
 *
 * @category    Magezoid
 * @package     Magezoid_LogManager
 * @copyright   Copyright (c) Magezoid
 */

namespace Magezoid\LogManager\Controller\Adminhtml\Logfile;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Controller\Adminhtml\System;
use Magento\Framework\App\Response\Http\FileFactory;

class Download extends System
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Magezoid_LogManager::log_viewer_download';

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Download constructor.
     * @param Context $context
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('file');
        $filePath = 'var/log/'. $fileName;

        return $this->fileFactory->create(
            $fileName,
            [
                'type'  => 'filename',
                'value' => $filePath
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
