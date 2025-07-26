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

namespace Magezoid\LogManager\Block;

use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\View\Element\Template;

class LogFile extends Template
{
    /**
     * @var File
     */
    protected $driver;

    /**
     * LogFile constructor.
     * @param Template\Context $context
     * @param File $driver
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        File $driver,
        array $data = []
    ) {
        $this->driver = $driver;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve download log file url
     *
     * @param string $fileName
     * @return string
     */
    public function getDownloadLogFileUrl($fileName)
    {
        return $this->getUrl('logviewer/logfile/download', ['file' => $fileName]);
    }

    /**
     * Retrieve view log file url
     *
     * @param string $fileName
     * @return string
     */
    public function getViewLogFileUrl($fileName)
    {
        return $this->getUrl('logviewer/logfile/view', ['file' => $fileName]);
    }

    /**
     * Retrieve delete log file url
     *
     * @param string $fileName
     * @return string
     */
    public function getDeleteLogFile($fileName)
    {
        return $this->getUrl('logviewer/logfile/delete', ['file' => $fileName]);
    }

    /**
     * Retrieve bulk delete URL for selected log files.
     *
     * @return string
     */
    public function getBulkDeleteUrl()
    {
        return $this->getUrl('logviewer/logfile/bulkDelete');
    }

    /**
     * Retrieve load previous log url
     *
     * @return string
     */
    public function getLoadPreviousLogUrl()
    {
        return $this->getUrl('logviewer/logfile/loadprevious') . '?isAjax=true';
    }

    /**
     * Retrieve file name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->getRequest()->getParam('file');
    }

    /**
     * Retrieve file size
     *
     * @param string $bytes
     * @param string $precision
     * @return string
     */
    protected function filesizeToReadableString($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Retrieve log files
     *
     * @return array
     */
    public function getLogFiles()
    {
        $page = (int) $this->getRequest()->getParam('page', 1);
        $limit = $this->getItemsPerPageCount();
        $search = trim((string) $this->getRequest()->getParam('q', ''));
        $defaultSortColumn = $this->getDefaultSortColumn();
        $sort = $this->getRequest()->getParam('sort', $defaultSortColumn);
        $defaultSortDirection = $this->getDefaultSortDirection();
        $direction = strtolower($this->getRequest()->getParam('dir', $defaultSortDirection));

        $path = BP . '/var/log/';
        $files = [];
        $dir = new \DirectoryIterator($path);

        $allowedFileExtensions = $this->getAllowedFileExtensions();
        foreach ($dir as $fileInfo) {
            if (!$fileInfo->isDot()) {
                $fileName = $fileInfo->getFilename();
                if ($search && stripos($fileName, $search) === false) {
                    continue;
                }

                if ($allowedFileExtensions) {
                    $extension = strtolower(strrchr($fileName, '.'));
                    if (!in_array($extension, $allowedFileExtensions, true)) {
                        continue;
                    }
                }

                $files[] = [
                    'name' => $fileInfo->getFilename(),
                    'size' => $fileInfo->getSize(),
                    'size_readable' => $this->filesizeToReadableString($fileInfo->getSize()),
                    'mod_time' => $fileInfo->getMTime(),
                    'mod_time_full' => date("F d Y H:i:s.", $fileInfo->getMTime()),
                ];
            }
        }

        usort($files, function ($a, $b) use ($sort, $direction) {
            $result = $a[$sort] <=> $b[$sort];
            return $direction === 'desc' ? -$result : $result;
        });

        $total = count($files);
        $totalPages = ceil($total / $limit);
        $start = ($page - 1) * $limit;

        return [
            'items' => array_slice($files, $start, $limit),
            'page' => $page,
            'total' => $total,
            'totalPages' => $totalPages,
            'search' => $search,
            'sort' => $sort,
            'dir' => $direction,
        ];
    }

    /**
     * Retrieve file content
     *
     * @param string $filePath
     * @param int $lines
     * @return string
     */
    public function tailFile($filePath, $lines)
    {
        $output = '';

        try {
            if ($this->driver->isReadable($filePath)) {
                $linesToRead = (int) $lines;
                $logLines = [];

                $file = new \SplFileObject($filePath, 'r');
                $file->seek(PHP_INT_MAX);
                $totalLines = $file->key();

                $startLine = max(0, $totalLines - $linesToRead);
                $file->seek($startLine);

                while (!$file->eof() && count($logLines) < $linesToRead) {
                    $logLines[] = rtrim($file->fgets(), "\n");
                }

                $output = implode("\n", $logLines);
            }
        } catch (\Exception $e) {
            $this->_logger->error($e);
        }

        return $output;
    }

    /**
     * Check if show pagination
     *
     * @param array $logs
     * @return bool
     */
    public function showPagination($logs)
    {
        $isShow = false;
        $limit = $this->getItemsPerPageCount();
        if ($logs['total'] > $limit) {
            $isShow = true;
        }
        return $isShow;
    }

    /**
     * Retrieve item per page count
     *
     * @return int
     */
    public function getItemsPerPageCount()
    {
        return (int)$this->_scopeConfig->getValue('log_viewer/general/items_per_page');
    }

    /**
     * Retrieve lines per page count
     *
     * @return int
     */
    public function getLinesToShowPerPageCount()
    {
        return (int)$this->_scopeConfig->getValue('log_viewer/general/lines_to_show');
    }

    /**
     * Retrieve default sort column
     *
     * @return string
     */
    public function getDefaultSortColumn()
    {
        return $this->_scopeConfig->getValue('log_viewer/general/default_sort_column');
    }

    /**
     * Retrieve default sort direction
     *
     * @return string
     */
    public function getDefaultSortDirection()
    {
        return $this->_scopeConfig->getValue('log_viewer/general/default_sort_dir');
    }

    /**
     * Retrieve allowed log file extensions
     *
     * @return array
     */
    public function getAllowedFileExtensions()
    {
        $extensions = [];
        $allowedExtensions = $this->_scopeConfig->getValue('log_viewer/general/allowed_extensions');
        if ($allowedExtensions) {
            $extensions = explode(',', $allowedExtensions);
        }
        return $extensions;
    }

    /**
     * Check is log can download
     *
     * @return bool
     */
    public function isDownloadAllowed()
    {
        return $this->_scopeConfig->isSetFlag('log_viewer/general/allow_download');
    }

    /**
     * Check is log can delete
     *
     * @return bool
     */
    public function isDeleteAllowed()
    {
        return $this->_scopeConfig->isSetFlag('log_viewer/general/allow_delete');
    }
}
