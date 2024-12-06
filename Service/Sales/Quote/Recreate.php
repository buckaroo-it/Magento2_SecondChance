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

namespace Buckaroo\Magento2SecondChance\Service\Sales\Quote;

use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\Message\ManagerInterface;
use Buckaroo\Magento2\Logging\Log;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\Quote;

class Recreate
{
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
    private $customerSession;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Log
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @param Cart                      $cart
     * @param CheckoutSession           $checkoutSession
     * @param CustomerSession           $customerSession
     * @param QuoteFactory              $quoteFactory
     * @param ManagerInterface          $messageManager
     * @param Log                       $logger
     * @param StoreManagerInterface     $storeManager
     */
    public function __construct(
        Cart $cart,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        QuoteFactory $quoteFactory,
        ManagerInterface $messageManager,
        Log $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->cart                 = $cart;
        $this->checkoutSession      = $checkoutSession;
        $this->customerSession      = $customerSession;
        $this->quoteFactory         = $quoteFactory;
        $this->messageManager       = $messageManager;
        $this->logger               = $logger;
        $this->storeManager         = $storeManager;
    }

    /**
     * Recreate the quote by Quote ID
     *
     * @param int $quoteId
     * @return Quote|null
     */
    public function recreateById($quoteId)
    {
        $this->logger->addDebug(__METHOD__ . '|quoteId|' . $quoteId);

        try {
            $oldQuote = $this->loadQuoteById($quoteId);
            if (!$oldQuote || !$oldQuote->getId()) {
                $this->logger->addDebug(__METHOD__ . '|No old quote found.');
                return null;
            }

            $quote = $this->createNewQuoteFromOld($oldQuote);
            if (!$quote) {
                return null;
            }

            $this->applyIncrementIdIfNeeded($quote);
            $this->applyCustomerData($quote, $oldQuote);
            $this->cart->setStoreId($oldQuote->getStoreId());
            $this->checkoutSession->replaceQuote($quote);
            $this->checkoutSession->setQuoteId($quote->getId());

            $this->logger->addDebug(__METHOD__ . '|Recreate complete|');
            return $quote;
        } catch (\Exception $e) {
            $this->handleError($e, 'Failed to recreate quote by ID.');
            return null;
        }
    }

    /**
     * Duplicate the quote based on the order and response
     *
     * @param Order $order
     * @param array $response
     * @return Quote|false
     */
    public function duplicate($order, $response = [])
    {
        $this->logger->addDebug(__METHOD__ . '|orderId|' . $order->getId());

        try {
            $oldQuote = $this->loadQuoteById($order->getQuoteId());
            if (!$oldQuote || !$oldQuote->getId()) {
                $this->logger->addDebug(__METHOD__ . '|No old quote found for duplication.');
                return false;
            }

            if ($this->isPayFastCheckout($response)) {
                $this->handlePayFastCheckout($oldQuote);
            }

            $quote = $this->createNewQuoteFromOld($oldQuote);
            if (!$quote) {
                return false;
            }

            // Additional custom logic if needed (e.g., apply different customer data)
            return $quote;
        } catch (\Exception $e) {
            $this->handleError($e, 'Failed to duplicate quote.');
            return false;
        }
    }

    /**
     * Load a quote by its ID
     *
     * @param int $quoteId
     * @return Quote|null
     */
    private function loadQuoteById($quoteId)
    {
        try {
            return $this->quoteFactory->create()->load($quoteId);
        } catch (\Exception $e) {
            $this->handleError($e, 'Could not load quote by ID.');
        }
        return null;
    }

    /**
     * Create a new quote and copy data from the old quote
     *
     * @param Quote $oldQuote
     * @return Quote|null
     */
    private function createNewQuoteFromOld(Quote $oldQuote)
    {
        $newQuote = $this->quoteFactory->create();
        try {
            $this->copyQuoteData($oldQuote, $newQuote);
            $this->initializeStoreForQuote($newQuote, (int)$oldQuote->getStoreId());
            $this->setPaymentMethod($oldQuote, $newQuote);
            $this->applyPaymentFromFlag($oldQuote, $newQuote);

            $this->checkoutSession->replaceQuote($newQuote);
            $this->cart->setQuote($newQuote)->save();
            return $newQuote;
        } catch (\Exception $e) {
            $this->handleError($e, 'Could not create new quote from old.');
            return null;
        }
    }

    /**
     * Copy relevant data from old quote to the new quote.
     * Avoid using merge() for finer control.
     *
     * @param Quote $oldQuote
     * @param Quote $newQuote
     * @return void
     */
    private function copyQuoteData(Quote $oldQuote, Quote $newQuote): void
    {
        // Copy customer data (basic)
        $newQuote->setCustomerId($oldQuote->getCustomerId());
        $newQuote->setCustomerEmail($oldQuote->getCustomerEmail());
        $newQuote->setCustomerGroupId($oldQuote->getCustomerGroupId());
        $newQuote->setCustomerIsGuest($oldQuote->getCustomerIsGuest());

        // Copy items
        foreach ($oldQuote->getAllVisibleItems() as $oldItem) {
            $newItem = clone $oldItem;
            $newItem->setQuote($newQuote);
            // Ensure the newItem ID is unset so it’s treated as a new entry
            $newItem->setId(null);
            $newQuote->addItem($newItem);
        }

        // Copy addresses if available
        if ($billingAddress = $oldQuote->getBillingAddress()) {
            $newBilling = clone $billingAddress;
            $newBilling->setId(null)->setQuote($newQuote);
            $newQuote->setBillingAddress($newBilling);
        }

        if ($shippingAddress = $oldQuote->getShippingAddress()) {
            $newShipping = clone $shippingAddress;
            $newShipping->setId(null)->setQuote($newQuote);
            $newQuote->setShippingAddress($newShipping);
        }

        // Copy shipping method
        if ($oldQuote->getShippingAddress() && $oldQuote->getShippingAddress()->getShippingMethod()) {
            $newQuote->getShippingAddress()->setShippingMethod($oldQuote->getShippingAddress()->getShippingMethod());
        }

        // Copy totals and save
        $newQuote->setTotalsCollectedFlag(false);
        $newQuote->collectTotals()->save();
    }

    /**
     * Initialize the store environment for the quote
     *
     * @param Quote $quote
     * @param int   $storeId
     * @return void
     */
    private function initializeStoreForQuote(Quote $quote, int $storeId): void
    {
        $store = $this->storeManager->getStore($storeId);
        $quote->setStore($store);
        $quote->setIsActive(true);
        $quote->collectTotals();
        $quote->save();
    }

    /**
     * Set payment method on new quote based on old quote's payment method
     *
     * @param Quote $oldQuote
     * @param Quote $newQuote
     * @return void
     */
    private function setPaymentMethod(Quote $oldQuote, Quote $newQuote): void
    {
        if ($oldQuote->getPayment() && $oldQuote->getPayment()->getMethod()) {
            $newQuote->getPayment()->setMethod($oldQuote->getPayment()->getMethod());
        }
    }

    /**
     * Apply 'buckaroo_payment_from' flag if exists
     *
     * @param Quote $oldQuote
     * @param Quote $newQuote
     * @return void
     */
    private function applyPaymentFromFlag(Quote $oldQuote, Quote $newQuote): void
    {
        $additionalData = $oldQuote->getPayment()->getAdditionalInformation();
        if (is_array($additionalData) && isset($additionalData['buckaroo_payment_from'])) {
            $newQuote->getPayment()->setAdditionalInformation('buckaroo_payment_from', $additionalData['buckaroo_payment_from']);
        }
    }

    /**
     * If a new increment ID was set in session, apply it to the quote
     *
     * @param Quote $quote
     * @return void
     */
    private function applyIncrementIdIfNeeded(Quote $quote): void
    {
        $newIncrementId = $this->customerSession->getSecondChanceNewIncrementId();
        if ($newIncrementId) {
            $this->logger->addDebug(__METHOD__ . '|Applying new increment ID|' . $newIncrementId);
            $this->customerSession->setSecondChanceNewIncrementId(false);
            $quote->setReservedOrderId($newIncrementId)->save();
            $this->checkoutSession->getQuote()->setReservedOrderId($newIncrementId)->save();
        }
    }

    /**
     * Apply customer data from old to new quote and session
     *
     * @param Quote $quote
     * @param Quote $oldQuote
     * @return void
     */
    private function applyCustomerData(Quote $quote, Quote $oldQuote): void
    {
        if ($email = $oldQuote->getBillingAddress()->getEmail()) {
            $quote->setCustomerEmail($email);
        }

        $quote->setCustomerIsGuest(true);
        if ($customer = $this->customerSession->getCustomer()) {
            $quote->setCustomerId($customer->getId());
            $quote->setCustomerEmail($customer->getEmail());
            $quote->setCustomerFirstname($customer->getFirstname());
            $quote->setCustomerLastname($customer->getLastname());
            $quote->setCustomerGroupId($customer->getGroupId());
            $quote->setCustomerIsGuest(false);
        }

        $quote->save();
    }

    /**
     * Handle PayFast checkout scenario by removing addresses and customer email from old quote
     *
     * @param Quote $oldQuote
     * @return void
     */
    private function handlePayFastCheckout(Quote $oldQuote): void
    {
        $this->logger->addDebug(__METHOD__ . '|PayFast checkout logic triggered.');

        // Remove customer email
        $oldQuote->setCustomerEmail(null);

        // Remove addresses
        if ($billingAddress = $oldQuote->getBillingAddress()) {
            $oldQuote->removeAddress($billingAddress->getId());
        }

        if ($shippingAddress = $oldQuote->getShippingAddress()) {
            $oldQuote->removeAddress($shippingAddress->getId());
        }
    }

    /**
     * Check if the current process is payfastcheckout
     *
     * @param array $response
     * @return bool
     */
    private function isPayFastCheckout(array $response): bool
    {
        return isset($response['add_service_action_from_magento']) && $response['add_service_action_from_magento'] === 'payfastcheckout';
    }

    /**
     * Handle and log exceptions
     *
     * @param \Exception $e
     * @param string $message
     * @return void
     */
    private function handleError(\Exception $e, string $message = 'An error occurred'): void
    {
        $this->logger->addError($e->getMessage());
        $this->messageManager->addErrorMessage(__($message));
    }
}
