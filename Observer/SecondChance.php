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
namespace Buckaroo\Magento2SecondChance\Observer;

class SecondChance implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Buckaroo\Magento2SecondChance\Model\SecondChanceRepository
     */
    protected $secondChanceRepository;

    protected $logging;

    protected $configProvider;

    /**
     * @param \Buckaroo\Magento2SecondChance\Model\SecondChanceRepository $secondChanceRepository,
     * @param \Buckaroo\Magento2\Logging\Log $logging,
     * @param \Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance $configProvider
     */
    public function __construct(
        \Buckaroo\Magento2SecondChance\Model\SecondChanceRepository $secondChanceRepository,
        \Buckaroo\Magento2\Logging\Log $logging,
        \Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance $configProvider
    ) {
        $this->secondChanceRepository = $secondChanceRepository;
        $this->logging                = $logging;
        $this->configProvider         = $configProvider;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->logging->addDebug(__METHOD__ . '|1|');
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();
        // $order = $observer->getData('order');
        if ($order && $this->configProvider->isSecondChanceEnabled($order->getStore())) {
            $this->logging->addDebug(__METHOD__ . '|2|');
            try {
                $this->secondChanceRepository->createSecondChance($order);
            } catch (\Exception $e) {
                $this->logging->addError('Could not create SC:' . $order->getIncrementId() . $e->getMessage());
            }
        }
        $this->logging->addDebug(__METHOD__ . '|3|');
    }
}
