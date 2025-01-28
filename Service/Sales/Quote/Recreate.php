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
namespace Buckaroo\Magento2SecondChance\Service\Sales\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Model\QuoteFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\ResourceModel\Quote\Address as QuoteAddressResource;
use Buckaroo\Magento2\Logging\Log;
use Magento\Store\Model\StoreManagerInterface;

class Recreate
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * @var QuoteAddressResource
     */
    protected $quoteAddressResource;

    /**
     * @var Log
     */
    protected $logger;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param CartRepositoryInterface $cartRepository
     * @param Cart $cart
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param QuoteFactory $quoteFactory
     * @param ProductFactory $productFactory
     * @param CartManagementInterface $quoteManagement
     * @param ManagerInterface $messageManager
     * @param QuoteAddressResource $quoteAddressResource
     * @param Log $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        Cart $cart,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        QuoteFactory $quoteFactory,
        ProductFactory $productFactory,
        CartManagementInterface $quoteManagement,
        ManagerInterface $messageManager,
        QuoteAddressResource $quoteAddressResource,
        Log $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->cartRepository       = $cartRepository;
        $this->cart                 = $cart;
        $this->checkoutSession      = $checkoutSession;
        $this->customerSession      = $customerSession;
        $this->quoteFactory         = $quoteFactory;
        $this->productFactory       = $productFactory;
        $this->quoteRepository      = $quoteRepository;
        $this->messageManager       = $messageManager;
        $this->quoteManagement      = $quoteManagement;
        $this->quoteAddressResource = $quoteAddressResource;
        $this->logger               = $logger;
        $this->storeManager         = $storeManager;
    }

    /**
     * Recreate the quote by resetting necessary fields
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote|false
     */
    protected function recreate($quote)
    {
        $this->logger->addDebug(__METHOD__ . '|1|');
        try {
            $quote->setIsActive(true);
            $quote->setTriggerRecollect('1');
            $quote->setReservedOrderId(null);
            $quote->setBuckarooFee(null);
            $quote->setBaseBuckarooFee(null);
            $quote->setBuckarooFeeTaxAmount(null);
            $quote->setBuckarooFeeBaseTaxAmount(null);
            $quote->setBuckarooFeeInclTax(null);
            $quote->setBaseBuckarooFeeInclTax(null);
            return $quote;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->addError($e->getMessage());
        }
        return false;
    }

    /**
     * Recreate the quote by Quote ID
     *
     * @param int $quoteId
     * @return \Magento\Quote\Model\Quote|null
     */
    public function recreateById($quoteId)
    {
        $this->logger->addDebug(__METHOD__ . '|1|' . $quoteId);
        try {
            $oldQuote = $this->quoteFactory->create()->load($quoteId);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->addError($e->getMessage());
            return null;
        }

        if ($oldQuote->getId()) {
            $this->logger->addDebug(__METHOD__ . '|5|');
            try {
                $quote = $this->quoteFactory->create();
                $quote->merge($oldQuote);
                $quote->save();

                // Set the correct store environment after merge
                $store = $this->storeManager->getStore($oldQuote->getStoreId());
                $quote->setStore($store);
                $quote->setIsActive(true);
                $quote->collectTotals();
                $quote->save();

            } catch (\Exception $e) {
                $this->logger->addError($e->getMessage());
                $this->messageManager->addErrorMessage($e->getMessage());
                return null;
            }

            $quote->setStoreId($oldQuote->getStoreId());
            $quote->getPayment()->setMethod($oldQuote->getPayment()->getMethod());

            $this->setPaymentFromFlag($quote, $oldQuote);

            $this->cart->setStoreId($oldQuote->getStoreId());
            $this->checkoutSession->replaceQuote($quote);
            $this->checkoutSession->setQuoteId($quote->getId());

            if ($newIncrementId = $this->customerSession->getSecondChanceNewIncrementId()) {
                $this->logger->addDebug(__METHOD__ . '|15|' . $newIncrementId);
                $this->customerSession->setSecondChanceNewIncrementId(false);
                $this->checkoutSession->getQuote()->setReservedOrderId($newIncrementId);
                $this->checkoutSession->getQuote()->save();
                $quote->setReservedOrderId($newIncrementId)->save();
            }

            if ($email = $oldQuote->getBillingAddress()->getEmail()) {
                $quote->setCustomerEmail($email);
            }

            $quote->setCustomerIsGuest(true);
            if ($customer = $this->customerSession->getCustomer()) {
                $quote->setCustomerId($customer->getId());
                $quote->setCustomerGroupId($customer->getGroupId());
                $quote->setCustomerIsGuest(false);
            }

            $this->logger->addDebug(__METHOD__ . '|30|');
            $quote = $this->recreate($quote);
            return $this->additionalMerge($oldQuote, $quote);
        }

        return null;
    }

    /**
     * Duplicate the quote based on the order and response
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $response
     * @return \Magento\Quote\Model\Quote|false
     */
    public function duplicate($order, $response = [])
    {
        $quote = $this->quoteFactory->create();
        try {
            $oldQuote = $this->quoteFactory->create()->load($order->getQuoteId());

            // Check if the action is 'payfastcheckout' and remove addresses if needed
            if (isset($response['add_service_action_from_magento']) && $response['add_service_action_from_magento'] === 'payfastcheckout') {
                $this->logger->addDebug(__METHOD__ . '|Handling payfastcheckout specific logic.');
                // Remove customer email
                $oldQuote->setCustomerEmail(null);

                // Remove billing and shipping addresses
                if ($billingAddress = $oldQuote->getBillingAddress()) {
                    $oldQuote->removeAddress($billingAddress->getId());
                }

                if ($shippingAddress = $oldQuote->getShippingAddress()) {
                    $oldQuote->removeAddress($shippingAddress->getId());
                }
            }

            $quote->merge($oldQuote);
            $quote->save();

            // Set the correct store environment after merge
            $store = $this->storeManager->getStore($oldQuote->getStoreId());
            $quote->setStore($store);
            $quote->setIsActive(true);
            $quote->collectTotals();
            $quote->save();

        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
            return false;
        }
        $quote = $this->recreate($quote);

        // Pass the response array to additionalMerge
        return $this->additionalMerge($oldQuote, $quote, $response);
    }

    /**
     * Additional merge logic with conditional handling.
     *
     * @param \Magento\Quote\Model\Quote $oldQuote
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $response
     * @return \Magento\Quote\Model\Quote
     */
    private function additionalMerge($oldQuote, $quote, $response = [])
    {
        $isPayFastCheckout = isset($response['add_service_action_from_magento']) && $response['add_service_action_from_magento'] === 'payfastcheckout';
        $this->logger->addDebug(__METHOD__ . '|isPayFastCheckout|' . ($isPayFastCheckout ? 'true' : 'false'));

        if (!$isPayFastCheckout) {
            if (!$oldQuote->getCustomerIsGuest() && $oldQuote->getCustomerId()) {
                $quote->setCustomerId($oldQuote->getCustomerId());
            }

            $quote->setCustomerEmail($oldQuote->getBillingAddress()->getEmail());
            $quote->setCustomerIsGuest($oldQuote->getCustomerIsGuest());

            if ($customer = $this->customerSession->getCustomer()) {
                $quote->setCustomerId($customer->getId());
                $quote->setCustomerEmail($customer->getEmail());
                $quote->setCustomerFirstname($customer->getFirstname());
                $quote->setCustomerLastname($customer->getLastname());
                $quote->setCustomerGroupId($customer->getGroupId());
                $quote->setCustomerIsGuest(false);
            }

            $quote->setBillingAddress(
                $oldQuote->getBillingAddress()->setQuote($quote)->setId(
                    $quote->getBillingAddress()->getId()
                )
            );
            $quote->setShippingAddress(
                $oldQuote->getShippingAddress()->setQuote($quote)->setId(
                    $quote->getShippingAddress()->getId()
                )
            );
            $quote->getShippingAddress()->setShippingMethod($oldQuote->getShippingAddress()->getShippingMethod());
            $this->quoteAddressResource->save($quote->getBillingAddress());
            $this->quoteAddressResource->save($quote->getShippingAddress());
        } else {
            $this->logger->addDebug(__METHOD__ . '|Skipping customer data reassignment due to payfastcheckout.');
        }

        try {
            $quote->save();
            $this->cart->setQuote($quote);
            $this->cart->save();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->addError($e->getMessage());
        }

        return $quote;
    }

    /**
     * Set payment from flag for the new quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote $oldQuote
     * @return void
     */
    protected function setPaymentFromFlag($quote, $oldQuote)
    {
        $additionalData = $oldQuote->getPayment()->getAdditionalInformation();
        if (is_array($additionalData) && isset($additionalData['buckaroo_payment_from'])) {
            $quote->getPayment()->setAdditionalInformation('buckaroo_payment_from', $additionalData['buckaroo_payment_from']);
        }
    }
}
