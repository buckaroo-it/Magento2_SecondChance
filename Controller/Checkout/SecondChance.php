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

namespace Buckaroo\Magento2SecondChance\Controller\Checkout;

use Buckaroo\Magento2\Logging\Log;
use Buckaroo\Magento2SecondChance\Model\SecondChanceRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

class SecondChance extends Action
{
    /**
     * @var Log
     */
    protected $logger;

    /**
     * @var SecondChanceRepository
     */
    protected $secondChanceRepository;

    /**
     * @param Context                $context
     * @param Log                    $logger
     * @param SecondChanceRepository $secondChanceRepository
     */
    public function __construct(
        Context $context,
        Log $logger,
        SecondChanceRepository $secondChanceRepository
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->secondChanceRepository = $secondChanceRepository;
    }

    /**
     * Execute action: Retrieves a token from request and handles the second chance logic.
     *
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface
    {
        $token = $this->getRequest()->getParam('token');
        if ($token) {
            $this->secondChanceRepository->getSecondChanceByToken($token);
        }
        return $this->handleRedirect('checkout', ['_fragment' => 'payment']);
    }

    /**
     * Redirects the response to a given path with arguments.
     *
     * @param string $path
     * @param array  $arguments
     * @return ResponseInterface
     */
    public function handleRedirect(string $path, array $arguments = []): ResponseInterface
    {
        return $this->_redirect($path, $arguments);
    }
}
