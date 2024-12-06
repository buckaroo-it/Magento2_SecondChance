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
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class SecondChance extends AbstractModel implements SecondChanceInterface
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var string
     */
    protected $_eventPrefix = 'buckaroo_magento2_second_chance';

    /**
     * @var SecondChanceInterfaceFactory
     */
    protected $secondChanceDataFactory;

    /**
     * @param Context                    $context
     * @param Registry                   $registry
     * @param SecondChanceInterfaceFactory $secondChanceDataFactory
     * @param DataObjectHelper           $dataObjectHelper
     * @param SecondChanceResource       $resource
     * @param SecondChanceCollection     $resourceCollection
     * @param array                      $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SecondChanceInterfaceFactory $secondChanceDataFactory,
        DataObjectHelper $dataObjectHelper,
        SecondChanceResource $resource,
        SecondChanceCollection $resourceCollection,
        array $data = []
    ) {
        $this->secondChanceDataFactory = $secondChanceDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve secondChance model as data object.
     *
     * @return SecondChanceInterface
     */
    public function getDataModel(): SecondChanceInterface
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
     * @inheritdoc
     */
    public function getSecondChanceId(): ?string
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSecondChanceId($secondChanceId): SecondChanceInterface
    {
        return $this->setData(self::ENTITY_ID, $secondChanceId);
    }
}
