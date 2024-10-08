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

use Magento\Framework\App\RequestInterface;

class ProcessHandleFailed implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var array
     */
    protected $response;

    protected $logging;

    protected $configProvider;

    protected $quoteRecreate;

    protected $customerSession;

    protected $checkoutSession;
    protected $request;


    /**
     * @param \Magento\Checkout\Model\Cart          $cart
     */
    public function __construct(
        \Buckaroo\Magento2\Logging\Log $logging,
        \Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance $configProvider,
        \Buckaroo\Magento2SecondChance\Service\Sales\Quote\Recreate $quoteRecreate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        RequestInterface $request
    ) {
        $this->logging         = $logging;
        $this->configProvider  = $configProvider;
        $this->quoteRecreate   = $quoteRecreate;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->request         = $request;
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
        $this->response = $this->request->getParams();
        $this->logging->addDebug(__METHOD__ . '|1|' . var_export($this->request->getParams(), true));

        $this->response = array_change_key_case($this->response, CASE_LOWER);
        if (!$order) {
            $this->logging->addDebug(__METHOD__ . '|no observer order|');
            $order = $this->checkoutSession->getLastRealOrder();
        }

        if ($order && $this->configProvider->isSecondChanceEnabled($order->getStore())) {
            $this->quoteRecreate->duplicate($order,$this->response);
            $this->customerSession->setSkipHandleFailedRecreate(1);
        }
    }
}
