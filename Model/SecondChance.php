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

namespace Buckaroo\Magento2SecondChance\Model;

use Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface;
use Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Buckaroo\Magento2SecondChance\Model\ResourceModel\SecondChance as SecondChanceResource;
use Buckaroo\Magento2SecondChance\Model\ResourceModel\SecondChance\Collection as SecondChanceCollection;

class SecondChance extends \Magento\Framework\Model\AbstractModel implements SecondChanceInterface
{
    protected $dataObjectHelper;

    protected $_eventPrefix = 'buckaroo_magento2_second_chance';

    protected $secondChanceDataFactory;

    /**
     * @param \Magento\Framework\Model\Context                                           $context
     * @param \Magento\Framework\Registry                                                $registry
     * @param SecondChanceInterfaceFactory                                               $secondChanceDataFactory
     * @param DataObjectHelper                                                           $dataObjectHelper
     * @param SecondChanceResource            $resource
     * @param SecondChanceCollection $resourceCollection
     * @param array                                                                      $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        SecondChanceInterfaceFactory $secondChanceDataFactory,
        DataObjectHelper $dataObjectHelper,
        SecondChanceResource $resource,
        SecondChanceCollection $resourceCollection,
        array $data = []
    ) {
        $this->secondChanceDataFactory = $secondChanceDataFactory;
        $this->dataObjectHelper        = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve secondChance model with secondChance data
     *
     * @return SecondChanceInterface
     */
    public function getDataModel()
    {
        $secondChanceData = $this->getData();

        $secondChanceDataObject = $this->secondChanceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $secondChanceDataObject,
            $secondChanceData,
            SecondChanceInterface::class
        );

        return $secondChanceDataObject;
    }

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
