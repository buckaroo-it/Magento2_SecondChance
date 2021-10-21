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

namespace Buckaroo\Magento2SecondChance\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SecondChanceRepositoryInterface
{

    /**
     * Save SecondChance
     *
     * @param  \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface $secondChance
     * @return \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface $secondChance
    );

    /**
     * Retrieve SecondChance
     *
     * @param  string $secondChanceId
     * @return \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($secondChanceId);

    /**
     * Retrieve SecondChance matching the specified criteria.
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete SecondChance
     *
     * @param  \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface $secondChance
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Buckaroo\Magento2SecondChance\Api\Data\SecondChanceInterface $secondChance
    );

    /**
     * Delete SecondChance by ID
     *
     * @param  string $secondChanceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($secondChanceId);
}
