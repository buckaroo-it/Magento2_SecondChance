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

class SecondChance extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $_eventPrefix = 'buckaroo_magento2_second_chance';

    protected $secondChanceDataFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param SecondChanceInterfaceFactory $secondChanceDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Buckaroo\Magento2SecondChance\Model\ResourceModel\SecondChance $resource
     * @param \Buckaroo\Magento2SecondChance\Model\ResourceModel\SecondChance\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        SecondChanceInterfaceFactory $secondChanceDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Buckaroo\Magento2SecondChance\Model\ResourceModel\SecondChance $resource,
        \Buckaroo\Magento2SecondChance\Model\ResourceModel\SecondChance\Collection $resourceCollection,
        array $data = []
    ) {
        $this->secondChanceDataFactory = $secondChanceDataFactory;
        $this->dataObjectHelper        = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve secondChance model with secondChance data
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
}
