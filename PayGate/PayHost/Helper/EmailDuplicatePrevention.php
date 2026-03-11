<?php

/*
 * Copyright (c) 2026 Payfast (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */

namespace PayGate\PayHost\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Cache\FrontendInterface;
use Psr\Log\LoggerInterface;

/**
 * Email Duplicate Prevention Helper
 */
class EmailDuplicatePrevention extends AbstractHelper
{
    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Email tracking cache lifetime in seconds
     */
    private const EMAIL_CACHE_LIFETIME = 3600; // 1 hour

    /**
     * Email cache key prefix
     */
    private const EMAIL_CACHE_PREFIX = 'payhost_email_';

    /**
     * @param Context $context
     * @param FrontendInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        FrontendInterface $cache,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->cache  = $cache;
        $this->logger = $logger;
    }

    /**
     * Check if an order email has already been sent
     *
     * @param string $orderId
     * @param string $emailType
     *
     * @return bool
     */
    public function hasEmailBeenSent(string $orderId, string $emailType): bool
    {
        $cacheKey = $this->getEmailCacheKey($orderId, $emailType);
        $result   = $this->cache->load($cacheKey);

        return $result !== false;
    }

    /**
     * Mark an order email as sent
     *
     * @param string $orderId
     * @param string $emailType
     *
     * @return void
     */
    public function markEmailAsSent(string $orderId, string $emailType): void
    {
        $cacheKey  = $this->getEmailCacheKey($orderId, $emailType);
        $timestamp = time();

        $this->cache->save(
            (string)$timestamp,
            $cacheKey,
            ['payhost_email'],
            self::EMAIL_CACHE_LIFETIME
        );

        $this->logger->info("Marked email as sent for order: {$orderId}, type: {$emailType}");
    }

    /**
     * Check if an invoice email has already been sent
     *
     * @param string $orderId
     * @param string $invoiceId
     *
     * @return bool
     */
    public function hasInvoiceEmailBeenSent(string $orderId, string $invoiceId): bool
    {
        $cacheKey = $this->getInvoiceEmailCacheKey($orderId, $invoiceId);
        $result   = $this->cache->load($cacheKey);

        return $result !== false;
    }

    /**
     * Mark an invoice email as sent
     *
     * @param string $orderId
     * @param string $invoiceId
     *
     * @return void
     */
    public function markInvoiceEmailAsSent(string $orderId, string $invoiceId): void
    {
        $cacheKey  = $this->getInvoiceEmailCacheKey($orderId, $invoiceId);
        $timestamp = time();

        $this->cache->save(
            (string)$timestamp,
            $cacheKey,
            ['payhost_email'],
            self::EMAIL_CACHE_LIFETIME
        );

        $this->logger->info("Marked invoice email as sent for order: {$orderId}, invoice: {$invoiceId}");
    }

    /**
     * Get cache key for email tracking
     *
     * @param string $orderId
     * @param string $emailType
     *
     * @return string
     */
    private function getEmailCacheKey(string $orderId, string $emailType): string
    {
        return self::EMAIL_CACHE_PREFIX . "order_{$orderId}_{$emailType}";
    }

    /**
     * Get cache key for invoice email tracking
     *
     * @param string $orderId
     * @param string $invoiceId
     *
     * @return string
     */
    private function getInvoiceEmailCacheKey(string $orderId, string $invoiceId): string
    {
        return self::EMAIL_CACHE_PREFIX . "invoice_{$orderId}_{$invoiceId}";
    }

    /**
     * Clean up expired email tracking entries
     *
     * @return void
     */
    public function cleanupExpiredEmailTracking(): void
    {
        $this->cache->clean(['payhost_email']);
        $this->logger->info("Cleaned up expired PayHost email tracking entries");
    }
}
