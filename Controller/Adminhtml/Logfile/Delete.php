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

use Magezoid\LogManager\Block\LogFile;
use Magezoid\LogManager\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Controller\Adminhtml\System;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem\Driver\File;

class Delete extends System
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Magezoid_LogManager::log_viewer_delete';

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var LogFile
     */
    protected $logFile;

    /**
     * @var File
     */
    protected $driver;

    /**
     * Delete constructor.
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param LogFile $logFile
     * @param File $driver
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        LogFile $logFile,
        File $driver
    ) {
        $this->fileFactory = $fileFactory;
        $this->logFile = $logFile;
        $this->driver = $driver;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function execute()
    {
        $fileName = $this->getRequest()->getParam('file');
        $file = BP . '/var/log/' . $fileName;
        $fp = $this->driver->fileOpen($file, "r+");
        ftruncate($fp, 0);
        $this->driver->fileClose($fp);
        $this->messageManager->addSuccessMessage(__("File content of %1 has been deleted", $fileName));
        $this->_redirect('logviewer/logfile/view', ['file' => $fileName]);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
