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
declare (strict_types = 1);

namespace Buckaroo\Magento2SecondChance\Model\Data;

use Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

class SecondChance extends AbstractExtensibleObject implements SecondChanceInterface
{
    /**
     * @inheritdoc
     */
    public function getSecondChanceId(): ?string
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSecondChanceId(string $secondChanceId): SecondChanceInterface
    {
        return $this->setData(self::ENTITY_ID, $secondChanceId);
    }
}
