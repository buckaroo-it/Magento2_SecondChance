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

namespace Buckaroo\Magento2SecondChance\Cron;

use Buckaroo\Magento2SecondChance\Model\SecondChanceRepository;
use Buckaroo\Magento2\Logging\Log;
use Magento\Store\Api\StoreRepositoryInterface;

class SecondChancePrune
{
    /**
     * @var Log
     */
    protected $logging;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var SecondChanceRepository
     */
    protected $secondChanceRepository;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param Log                      $logging
     * @param SecondChanceRepository   $secondChanceRepository
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        Log $logging,
        SecondChanceRepository $secondChanceRepository
    ) {
        $this->storeRepository = $storeRepository;
        $this->logging = $logging;
        $this->secondChanceRepository = $secondChanceRepository;
    }

    /**
     * Execute prune cron job to remove old second chance records.
     *
     * @return $this
     */
    public function execute(): self
    {
        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            $this->secondChanceRepository->deleteOlderRecords($store);
        }
        return $this;
    }
}
