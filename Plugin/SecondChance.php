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
namespace Buckaroo\Magento2SecondChance\Plugin;

class SecondChance
{
    protected $customerSession;
    protected $logger;
    protected $configProvider;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Buckaroo\Magento2\Logging\Log $logger,
        \Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance $configProvider
    ) {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
    }

    public function aroundShouldSkipFurtherEventHandling()
    {
        return $this->customerSession->getSecondChanceRecreate();
    }

    public function aroundIsNeedRecreate(\Buckaroo\Magento2\Plugin\ShippingMethodManagement $subject, $proceed, $store)
    {
        return $this->configProvider->isSecondChanceEnabled($store);
    }
    
    public function aroundGetSkipHandleFailedRecreate()
    {
        return $this->customerSession->getSkipHandleFailedRecreate();
    }

    public function aroundSetSkipHandleFailedRecreate($value)
    {
        return $this->customerSession->setSkipHandleFailedRecreate($value);
    }
}
