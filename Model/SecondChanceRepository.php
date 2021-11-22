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

use Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterfaceFactory;
use Buckaroo\Magento2SecondChance\Api\Data\SecondChanceSearchResultsInterfaceFactory;
use Buckaroo\Magento2SecondChance\Api\SecondChanceRepositoryInterface;
use Buckaroo\Magento2SecondChance\Model\ResourceModel\SecondChance as ResourceSecondChance;
use Buckaroo\Magento2SecondChance\Model\ResourceModel\SecondChance\CollectionFactory as SecondChanceCollectionFactory;
use Buckaroo\Magento2\Model\Method\PayPerEmail;
use Buckaroo\Magento2\Model\Method\Transfer;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface;

class SecondChanceRepository implements SecondChanceRepositoryInterface
{

    protected $secondChanceFactory;

    protected $resource;

    protected $searchResultsFactory;

    protected $extensibleDataObjectConverter;

    protected $secondChanceCollectionFactory;

    private $storeManager;

    protected $dataSecondChanceFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $extensionAttributesJoinProcessor;

    private $collectionProcessor;

    protected $logging;

    protected $configProvider;

    protected $checkoutSession;

    protected $customerSession;

    protected $dateTime;

    protected $mathRandom;

    protected $orderFactory;

    protected $customerFactory;

    private $orderIncrementIdChecker;

    protected $quoteFactory;

    protected $addressFactory;

    protected $stockRegistry;

    protected $inlineTranslation;

    protected $transportBuilder;

    protected $addressRenderer;

    protected $paymentHelper;

    protected $identityContainer;
    
    protected $quoteRecreate;

    /**
     * @param ResourceSecondChance                      $resource
     * @param SecondChanceFactory                       $secondChanceFactory
     * @param SecondChanceInterfaceFactory              $dataSecondChanceFactory
     * @param SecondChanceCollectionFactory             $secondChanceCollectionFactory
     * @param SecondChanceSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper                          $dataObjectHelper
     * @param DataObjectProcessor                       $dataObjectProcessor
     * @param StoreManagerInterface                     $storeManager
     * @param CollectionProcessorInterface              $collectionProcessor
     * @param JoinProcessorInterface                    $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter             $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceSecondChance $resource,
        SecondChanceFactory $secondChanceFactory,
        SecondChanceInterfaceFactory $dataSecondChanceFactory,
        SecondChanceCollectionFactory $secondChanceCollectionFactory,
        SecondChanceSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Buckaroo\Magento2\Logging\Log $logging,
        \Buckaroo\Magento2SecondChance\Model\ConfigProvider\SecondChance $configProvider,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\OrderIncrementIdChecker $orderIncrementIdChecker,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\Order\Email\Container\ShipmentIdentity $identityContainer,
        \Buckaroo\Magento2SecondChance\Service\Sales\Quote\Recreate $quoteRecreate
    ) {
        $this->resource                         = $resource;
        $this->secondChanceFactory              = $secondChanceFactory;
        $this->secondChanceCollectionFactory    = $secondChanceCollectionFactory;
        $this->searchResultsFactory             = $searchResultsFactory;
        $this->dataObjectHelper                 = $dataObjectHelper;
        $this->dataSecondChanceFactory          = $dataSecondChanceFactory;
        $this->dataObjectProcessor              = $dataObjectProcessor;
        $this->storeManager                     = $storeManager;
        $this->collectionProcessor              = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter    = $extensibleDataObjectConverter;
        $this->logging                          = $logging;
        $this->configProvider                   = $configProvider;
        $this->checkoutSession                  = $checkoutSession;
        $this->customerSession                  = $customerSession;
        $this->mathRandom                       = $mathRandom;
        $this->dateTime                         = $dateTime;
        $this->orderFactory                     = $orderFactory;
        $this->customerFactory                  = $customerFactory;
        $this->orderIncrementIdChecker          = $orderIncrementIdChecker;
        $this->quoteFactory                     = $quoteFactory;
        $this->addressFactory                   = $addressFactory;
        $this->stockRegistry                    = $stockRegistry;
        $this->inlineTranslation                = $inlineTranslation;
        $this->transportBuilder                 = $transportBuilder;
        $this->addressRenderer                  = $addressRenderer;
        $this->paymentHelper                    = $paymentHelper;
        $this->identityContainer                = $identityContainer;
        $this->quoteRecreate                    = $quoteRecreate;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        SecondChanceInterface $secondChance
    ) {

        $secondChanceData = $this->extensibleDataObjectConverter->toNestedArray(
            $secondChance,
            [],
            SecondChanceInterface::class
        );

        $secondChanceModel = $this->secondChanceFactory->create()->setData($secondChanceData);

        try {
            $this->resource->save($secondChanceModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the secondChance: %1',
                    $exception->getMessage()
                )
            );
        }
        return $secondChanceModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($secondChanceId)
    {
        $secondChance = $this->secondChanceFactory->create();
        $this->resource->load($secondChance, $secondChanceId);
        if (!$secondChance->getId()) {
            throw new NoSuchEntityException(__('SecondChance with id "%1" does not exist.', $secondChanceId));
        }
        return $secondChance->getDataModel();
    }

    public function getByOrderId(string $orderId): SecondChanceInterface
    {
        $this->logging->addDebug(__METHOD__ . '|orderId|' . $orderId);
        /**
         * @var SecondChanceInterface $secondChanceEntity
         */
        $secondChanceEntity = $this->secondChanceFactory->create();
        $this->resource->load($secondChanceEntity, $orderId, SecondChanceInterface::ORDER_ID);

        if (!$secondChanceEntity->getId()) {
            throw new NoSuchEntityException(__('SecondChance with orderId "%1" does not exist.', $orderId));
        }

        $this->logging->addDebug(__METHOD__ . '|secondChanceEntity->getId|' . $secondChanceEntity->getId());

        return $secondChanceEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->secondChanceCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            SecondChanceInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        SecondChanceInterface $secondChance
    ) {
        try {
            $secondChanceModel = $this->secondChanceFactory->create();
            $this->resource->load($secondChanceModel, $secondChance->getId());
            $this->resource->delete($secondChanceModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __(
                    'Could not delete the SecondChance: %1',
                    $exception->getMessage()
                )
            );
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($secondChanceId)
    {
        return $this->delete($this->get($secondChanceId));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByOrderId($orderId)
    {
        $this->logging->addDebug(__METHOD__ . '|1|');
        if ($orderId) {
            $secondChance = $this->getByOrderId($orderId);
            return $this->delete($secondChance);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOlderRecords($store)
    {
        $days = (int) $this->configProvider->getSecondChancePruneDays($store);

        if ($days <= 0) {
            return false;
        }

        $this->logging->addDebug(__METHOD__ . '|'. $store->getId(). '|$days|' . $days);

        $connection = $this->resource->getConnection();
        try {
            $ageCondition = $connection->prepareSqlCondition(
                'created_at',
                ['lt' => new \Zend_Db_Expr('NOW() - INTERVAL ? DAY')]
            );
            $storeCondition = $connection->prepareSqlCondition('store_id', $store->getId());
            $connection->delete(
                $this->resource->getMainTable(),
                [$ageCondition => $days, $storeCondition]
            );
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createSecondChance($order)
    {
        $this->logging->addDebug(__METHOD__ . '|1|' . $order->getIncrementId());
        if (!$this->customerSession->getSkipSecondChance()) {
            $this->logging->addDebug(__METHOD__ . '|2|');
            $secondChance = $this->secondChanceFactory->create();
            $secondChance->setData(
                [
                    'order_id'   => $order->getIncrementId(),
                    'token'      => $this->mathRandom->getUniqueHash(),
                    'store_id'   => $order->getStoreId(),
                    'created_at' => $this->dateTime->gmtDate(),
                ]
            );
            return $secondChance->save();
        }
        $this->logging->addDebug(__METHOD__ . '|3|');
        $this->customerSession->setSkipSecondChance(false);
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSecondChanceByToken($token)
    {
        $secondChance = $this->secondChanceFactory->create();
        $collection   = $secondChance->getCollection()
            ->addFieldToFilter(
                'token',
                ['eq' => $token]
            );
        foreach ($collection as $item) {
            $order = $this->orderFactory->create()->loadByIncrementId($item->getOrderId());

            if (!$order->getCustomerId() && $customerEmail = $order->getCustomerEmail()) {
                if ($customer = $this->customerFactory->create()->setWebsiteId(
                    $order->getStoreId()
                )->loadByEmail($customerEmail)
                ) {
                    if ($customer->getId()) {
                        $this->setCustomerAddress($customer, $order);
                    }
                }
            }
            $this->customerSession->setSecondChanceRecreate($order->getQuoteId());
            $newOrderId = $this->setAvailableIncrementId($item->getOrderId(), $item, $order);
            $this->customerSession->setSecondChanceNewIncrementId($newOrderId);
            $this->quoteRecreate->recreateById($order->getQuoteId());
        }
    }

    private function setAvailableIncrementId($orderId, $item, $order)
    {
        $this->logging->addDebug(__METHOD__ . '|setAvailableIncrementId|' . $orderId);
        for ($i = 1; $i < 100; $i++) {
            $newOrderId = $orderId . '-' . $i;
            if (!$this->orderIncrementIdChecker->isIncrementIdUsed($newOrderId)) {
                $this->logging->addDebug(__METHOD__ . '|setReservedOrderId|' . $newOrderId);
                $this->checkoutSession->getQuote()->setReservedOrderId($newOrderId);
                $this->checkoutSession->getQuote()->save();

                $quote = $this->quoteFactory->create()->load($order->getQuoteId());
                $quote->setReservedOrderId($newOrderId)->save();

                $this->customerSession->setSkipSecondChance($newOrderId);

                $item->setLastOrderId($newOrderId);
                $item->save();
                return $newOrderId;
            }
        }
    }

    public function getSecondChanceCollection($step, $store)
    {
        $final_status = $this->configProvider->getFinalStatus();

        if ($step == 2) {
            if (!$this->configProvider->isSecondChanceEmail2($store)) {
                return false;
            }
        } else {
            if (!$this->configProvider->isSecondChanceEmail($store)) {
                return false;
            }
        }

        $timing = $this->configProvider->getSecondChanceTiming($store) +
            ($step == 2 ? $this->configProvider->getSecondChanceTiming2($store) : 0);

        $this->logging->addDebug(__METHOD__ . '|secondChance timing|' . $timing);

        $secondChance = $this->secondChanceFactory->create();
        $collection   = $secondChance->getCollection()
            ->addFieldToFilter(
                'status',
                ['eq' => ($step == 2 && $this->configProvider->isSecondChanceEmail($store)) ? 1 : '']
            )
            ->addFieldToFilter(
                'store_id',
                ['eq' => $store->getId()]
            )
            ->addFieldToFilter('created_at', ['lteq' => new \Zend_Db_Expr('NOW() - INTERVAL ' . $timing . ' HOUR')])
            ->addFieldToFilter('created_at', ['gteq' => new \Zend_Db_Expr('NOW() - INTERVAL 5 DAY')])
            ->setOrder('created_at', 'DESC');

        $flag = $this->dateTime->gmtDate();

        foreach ($collection as $item) {
            $order = $this->orderFactory->create()->loadByIncrementId($item->getOrderId());

            if (!$this->configProvider->isMultipleEmailsSend($store)) {
                if ($this->checkForMultipleEmail($order, $flag)) {
                    $this->setFinalStatus($item, $final_status);
                    continue;
                }
            }

            //BP-896 skip Transfer method
            $payment = $order->getPayment();
            if (in_array($payment->getMethod(), [Transfer::PAYMENT_METHOD_CODE, PayPerEmail::PAYMENT_METHOD_CODE])) {
                $this->setFinalStatus($item, $final_status);
                continue;
            }

            if ($item->getLastOrderId() != null
                && $last_order = $this->orderFactory->create()->loadByIncrementId($item->getLastOrderId())
            ) {
                if ($last_order->hasInvoices()) {
                    $this->setFinalStatus($item, $final_status);
                    continue;
                }
            }

            if ($order->hasInvoices()) {
                $this->setFinalStatus($item, $final_status);
            } else {
                if ($this->configProvider->getNoSendSecondChance($store)) {
                    $this->logging->addDebug(__METHOD__ . '|getNoSendSecondChance|');
                    if ($this->checkOrderProductsIsInStock($order)) {
                        $this->logging->addDebug(__METHOD__ . '|checkOrderProductsIsInStock|');
                        $this->sendMail($order, $item, $step);
                    }
                } else {
                    $this->logging->addDebug(__METHOD__ . '|else getNoSendSecondChance|');
                    $this->sendMail($order, $item, $step);
                }
            }
        }
    }

    public function sendMail($order, $secondChance, $step)
    {
        $this->logging->addDebug(__METHOD__ . '|sendMail start|');

        $store = $order->getStore();
        $vars  = [
            'order'                    => $order,
            'billing'                  => $order->getBillingAddress(),
            'payment_html'             => $this->getPaymentHtml($order),
            'store'                    => $store,
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress'  => $this->getFormattedBillingAddress($order),
            'secondChanceToken'        => $secondChance->getToken(),
        ];

        $templateId = ($step == 1) ?
        $this->configProvider->getSecondChanceTemplate($store) :
        $this->configProvider->getSecondChanceTemplate2($store);

        $this->logging->addDebug(__METHOD__ . '|TemplateIdentifier|' . $templateId);

        $this->inlineTranslation->suspend();
        $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $store->getId(),
                ]
            )->setTemplateVars($vars)
            ->setFrom(
                [
                    'email' => $this->configProvider->getFromEmail($store),
                    'name'  => $this->configProvider->getFromName($store),
                ]
            )->addTo($order->getCustomerEmail());

        if (!isset($transport)) {
            $transport = $this->transportBuilder->getTransport();
        }

        try {
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $secondChance->setStatus($step);
            $secondChance->save();
            $this->logging->addDebug(__METHOD__ . '|secondChanceEmail is sended to|' . $order->getCustomerEmail());
        } catch (\Exception $exception) {
            $this->logging->addDebug(__METHOD__ . '|log failed email send|' . $exception->getMessage());
        }
    }

    /**
     * Render shipping address into html.
     *
     * @param  Order $order
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
        ? null
        : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Render billing address into html.
     *
     * @param  Order $order
     * @return string|null
     */
    protected function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * Returns payment info block as HTML.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return string
     * @throws \Exception
     */
    private function getPaymentHtml(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

    private function checkOrderProductsIsInStock($order)
    {
        if ($allItems = $order->getAllItems()) {
            foreach ($allItems as $orderItem) {
                $product = $orderItem->getProduct();
                if ($sku = $product->getData('sku')) {
                    $stock = $this->stockRegistry->getStockItemBySku($sku);

                    if ($orderItem->getProductType() == Type::TYPE_SIMPLE) {
                        //check is in stock flag and if there is enough qty
                        if ((!$stock->getIsInStock())
                            || ((int) ($orderItem->getQtyOrdered()) > (int) ($stock->getQty()))
                        ) {
                            $this->logging->addDebug(
                                __METHOD__ . '|not getIsInStock|' . $orderItem->getProduct()->getId()
                            );
                            return false;
                        }
                    } else {
                        //other product types - bundle / configurable, etc, check only flag
                        if (!$stock->getIsInStock()) {
                            $this->logging->addDebug(
                                __METHOD__ . '|not getIsInStock|' . $orderItem->getProduct()->getSku()
                            );
                            return false;
                        }
                    }

                }
            }
        }
        return true;
    }

    private function setFinalStatus($item, $status)
    {
        $item->setStatus($status);
        return $item->save();
    }

    private function setCustomerAddress($customer, $order)
    {
        $address = $this->addressFactory->create();
        $address->setData($order->getBillingAddress()->getData());
        $customerId = $customer->getId();

        $address->setCustomerId($customerId)
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('0')
            ->setSaveInAddressBook('1');
        $address->save();

        if (!$order->getIsVirtual()) {
            $address = $this->addressFactory->create();
            $address->setData($order->getShippingAddress()->getData());

            $address->setCustomerId($customerId)
                ->setIsDefaultBilling('0')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1');
            $address->save();
        }
    }

    public function checkForMultipleEmail($order, $flag)
    {
        $multipleEmail = $this->checkoutSession->getMultipleEmail();
        if (!empty($multipleEmail[$flag][$order->getCustomerEmail()])) {
            return true;
        }
        $multipleEmail[$flag][$order->getCustomerEmail()] = 1;
        $this->checkoutSession->setMultipleEmail($multipleEmail);
        return false;
    }
}
