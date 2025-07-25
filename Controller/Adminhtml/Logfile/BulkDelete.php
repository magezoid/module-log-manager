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
use Magento\Backend\App\Action\Context;
use Magento\Backend\Controller\Adminhtml\System;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem\Driver\File;

class BulkDelete extends System
{
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
     * Constructor
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
     * Execute bulk delete
     */
    public function execute()
    {
        $fileNames = $this->getRequest()->getParam('files', []);
        $deleted = 0;
        $errors = [];
    
        if (!is_array($fileNames) || empty($fileNames)) {
            $this->messageManager->addErrorMessage(__('Please select log files to delete.'));
            return $this->_redirect('logviewer/logfile/index');
        }
    
        foreach ($fileNames as $fileName) {
            $file = BP . '/var/log/' . $fileName;
            try {
                if ($this->driver->isExists($file) && $this->driver->isWritable($file)) {
                    $this->driver->deleteFile($file); // real deletion
                    $deleted++;
                } else {
                    $errors[] = $fileName;
                }
            } catch (\Exception $e) {
                $errors[] = $fileName;
            }
        }
    
        if ($deleted) {
            $this->messageManager->addSuccessMessage(__('%1 file(s) deleted successfully.', $deleted));
        }
    
        if (!empty($errors)) {
            $this->messageManager->addErrorMessage(__('Some files could not be deleted: %1', implode(', ', $errors)));
        }
    
        return $this->_redirect('logviewer/logfile/index');
    }
    

    /**
     * Check ACL permissions
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
