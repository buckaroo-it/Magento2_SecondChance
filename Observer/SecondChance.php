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

namespace Buckaroo\Magento2SecondChance\Observer;

use Buckaroo\Magento2SecondChance\Model\SecondChanceRepository;
use Buckaroo\Magento2\Logging\Log;
use Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance as ConfigProvider;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SecondChance implements ObserverInterface
{
    /**
     * @var SecondChanceRepository
     */
    protected $secondChanceRepository;

    /**
     * @var Log
     */
    protected $logging;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @param SecondChanceRepository $secondChanceRepository
     * @param Log                    $logging
     * @param ConfigProvider         $configProvider
     */
    public function __construct(
        SecondChanceRepository $secondChanceRepository,
        Log $logging,
        ConfigProvider $configProvider
    ) {
        $this->secondChanceRepository = $secondChanceRepository;
        $this->logging = $logging;
        $this->configProvider = $configProvider;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $this->logging->addDebug(__METHOD__ . ' | Start');

        /** @var \Magento\Sales\Model\Order|null $order */
        $order = $observer->getEvent()->getOrder();
        if ($order && $this->configProvider->isSecondChanceEnabled($order->getStore())) {
            $this->logging->addDebug(__METHOD__ . ' | Creating second chance for order: ' . $order->getIncrementId());
            try {
                $this->secondChanceRepository->createSecondChance($order);
            } catch (\Exception $e) {
                $this->logging->addError('Could not create SecondChance for order ' . $order->getIncrementId() . ': ' . $e->getMessage());
            }
        }

        $this->logging->addDebug(__METHOD__ . ' | End');
    }
}
