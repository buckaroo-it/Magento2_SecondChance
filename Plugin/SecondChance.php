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

namespace Buckaroo\Magento2SecondChance\Plugin;

use Buckaroo\Magento2\Plugin\ShippingMethodManagement;
use Magento\Customer\Model\Session as CustomerSession;
use Buckaroo\Magento2\Logging\Log;
use Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance as ConfigProvider;

class SecondChance
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Log
     */
    protected $logger;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * @param CustomerSession $customerSession
     * @param Log             $logger
     * @param ConfigProvider  $configProvider
     */
    public function __construct(
        CustomerSession $customerSession,
        Log $logger,
        ConfigProvider $configProvider
    ) {
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
    }

    /**
     * Plugin around method for skipping event handling
     *
     * @return mixed
     */
    public function aroundShouldSkipFurtherEventHandling(): mixed
    {
        return $this->customerSession->getSecondChanceRecreate();
    }

    /**
     * Plugin around method to check if we need to recreate the quote
     *
     * @param ShippingMethodManagement $subject
     * @param callable                                           $proceed
     * @param mixed                                              $store
     * @return bool
     */
    public function aroundIsNeedRecreate(
        ShippingMethodManagement $subject,
        callable $proceed,
        $store
    ): bool {
        return $this->configProvider->isSecondChanceEnabled($store);
    }

    /**
     * Plugin around method to get skip handle failed recreate flag
     *
     * @return mixed
     */
    public function aroundGetSkipHandleFailedRecreate(): mixed
    {
        return $this->customerSession->getSkipHandleFailedRecreate();
    }

    /**
     * Plugin around method to set skip handle failed recreate flag
     *
     * @param mixed $value
     * @return mixed
     */
    public function aroundSetSkipHandleFailedRecreate($value): mixed
    {
        return $this->customerSession->setSkipHandleFailedRecreate($value);
    }
}
