<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * It is available through the world-wide-web at this URL:
 * https://tldrlegal.com/license/mit-license
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@buckaroo.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@buckaroo.nl for more information.
 *
 * @copyright Copyright (c) Buckaroo B.V.
 * @license   https://tldrlegal.com/license/mit-license
 */
declare(strict_types=1);

namespace Buckaroo\Magento2SecondChance\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class SecondChance extends Value
{
    /**
     * Validate the entered timing value before saving.
     *
     * @return $this
     * @throws LocalizedException
     */
    public function save()
    {
        $value = (int) $this->getValue();
        $item = $this->toArray();

        // Determine allowed interval (24 or 72)
        $interval = ($item['field'] ?? '') === 'second_chance_timing2' ? 72 : 24;

        if ($value !== 0 && ($value < 0 || $value > $interval)) {
            throw new LocalizedException(
                __("Please enter a valid integer within 0 and $interval interval")
            );
        }

        $this->setValue((string)$value);
        return parent::save();
    }
}
