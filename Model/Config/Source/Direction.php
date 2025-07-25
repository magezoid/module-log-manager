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

class Direction implements ArrayInterface
{
    /**
     * Retrieve direction
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'asc', 'label' => 'Ascending'],
            ['value' => 'desc', 'label' => 'Descending']
        ];
    }
}
