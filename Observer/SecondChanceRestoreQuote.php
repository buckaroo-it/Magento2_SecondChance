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

use Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance as ConfigProvider;
use Magento\Customer\Model\Session as CustomerSession;
use Buckaroo\Magento2\Logging\Log;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SecondChanceRestoreQuote implements ObserverInterface
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Log
     */
    protected $logging;

    /**
     * @param ConfigProvider  $configProvider
     * @param CustomerSession $customerSession
     * @param Log             $logging
     */
    public function __construct(
        ConfigProvider $configProvider,
        CustomerSession $customerSession,
        Log $logging
    ) {
        $this->configProvider = $configProvider;
        $this->customerSession = $customerSession;
        $this->logging = $logging;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $quoteId = $this->customerSession->getSecondChanceRecreate();
        if ($quoteId) {
            try {
                $this->customerSession->setSecondChanceRecreate(false);
            } catch (\Exception $e) {
                $this->logging->addError('Could not clear SecondChanceRecreate for quote ' . $quoteId . ': ' . $e->getMessage());
            }
        }
    }
}
