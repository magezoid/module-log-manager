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

namespace Magezoid\LogManager\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ListPerPage implements ArrayInterface
{
    /**
     * Retrieve list per page count
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 5, 'label' => '5'],
            ['value' => 10, 'label' => '10'],
            ['value' => 25, 'label' => '25'],
            ['value' => 50, 'label' => '50'],
            ['value' => 100, 'label' => '100']
        ];
    }
}
