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

class SecondChance extends \Magento\Framework\Api\AbstractExtensibleObject implements SecondChanceInterface
{

    /**
     * Get secondChance_id
     *
     * @return string|null
     */
    public function getSecondChanceId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Set secondChance_id
     *
     * @param  string $secondChanceId
     * @return \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface
     */
    public function setSecondChanceId($secondChanceId)
    {
        return $this->setData(self::ENTITY_ID, $secondChanceId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param  \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

}
