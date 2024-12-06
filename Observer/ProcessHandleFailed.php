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

use Buckaroo\Magento2\Logging\Log;
use Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance as ConfigProvider;
use Buckaroo\Magento2SecondChance\Service\Sales\Quote\Recreate;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\Order;

class ProcessHandleFailed implements ObserverInterface
{
    /**
     * @var Log
     */
    protected $logging;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @var Recreate
     */
    protected $quoteRecreate;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Constructor
     *
     * @param Log            $logging
     * @param ConfigProvider $configProvider
     * @param Recreate       $quoteRecreate
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param RequestInterface $request
     */
    public function __construct(
        Log $logging,
        ConfigProvider $configProvider,
        Recreate $quoteRecreate,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        RequestInterface $request
    ) {
        $this->logging = $logging;
        $this->configProvider = $configProvider;
        $this->quoteRecreate = $quoteRecreate;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->request = $request;
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

        /** @var Order|null $order */
        $order = $observer->getEvent()->getOrder();
        $response = $this->request->getParams();
        $this->logging->addDebug(__METHOD__ . ' | Response: ' . var_export($response, true));

        // Normalize keys to lowercase
        $response = array_change_key_case($response, CASE_LOWER);

        if (!$order) {
            $this->logging->addDebug(__METHOD__ . ' | No order in observer, trying checkout session last real order');
            $order = $this->checkoutSession->getLastRealOrder();
        }

        if ($order && $this->configProvider->isSecondChanceEnabled($order->getStore())) {
            $this->quoteRecreate->duplicate($order, $response);
            $this->customerSession->setSkipHandleFailedRecreate(1);
        }

        $this->logging->addDebug(__METHOD__ . ' | End');
    }
}
