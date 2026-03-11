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
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Cache Lock Helper for preventing duplicate request processing
 */
class Lock extends AbstractHelper
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
     * Default lock timeout in seconds
     */
    private const DEFAULT_LOCK_TIMEOUT = 300; // 5 minutes

    /**
     * Lock prefix for cache keys
     */
    private const LOCK_PREFIX = 'payhost_lock_';

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
     * Acquire a lock for the given identifier
     *
     * @param string $identifier
     * @param int $timeout
     *
     * @return bool
     * @throws LocalizedException
     */
    public function acquireLock(string $identifier, int $timeout = self::DEFAULT_LOCK_TIMEOUT): bool
    {
        $lockKey   = $this->getLockKey($identifier);
        $lockValue = time() + $timeout;

        // Check if lock already exists and is still valid
        if ($this->isLocked($identifier)) {
            $this->logger->info("Lock already exists for identifier: {$identifier}");

            return false;
        }

        // Set the lock
        $this->cache->save(
            (string)$lockValue,
            $lockKey,
            ['payhost_lock'],
            $timeout
        );

        $this->logger->info("Lock acquired for identifier: {$identifier}");

        return true;
    }

    /**
     * Release a lock for the given identifier
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function releaseLock(string $identifier): bool
    {
        $lockKey = $this->getLockKey($identifier);
        $result  = $this->cache->remove($lockKey);

        $this->logger->info("Lock released for identifier: {$identifier}");

        return $result;
    }

    /**
     * Check if a lock exists and is still valid
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function isLocked(string $identifier): bool
    {
        $lockKey   = $this->getLockKey($identifier);
        $lockValue = $this->cache->load($lockKey);

        if ($lockValue === false) {
            return false;
        }

        $expiry = (int)$lockValue;
        if ($expiry < time()) {
            // Lock has expired, remove it
            $this->cache->remove($lockKey);

            return false;
        }

        return true;
    }

    /**
     * Get lock key for cache
     *
     * @param string $identifier
     *
     * @return string
     */
    private function getLockKey(string $identifier): string
    {
        return self::LOCK_PREFIX . md5($identifier);
    }

    /**
     * Create a lock identifier for order processing
     *
     * @param string $orderId
     * @param string $payRequestId
     *
     * @return string
     */
    public function createOrderLockIdentifier(string $orderId, string $payRequestId): string
    {
        return "order_{$orderId}_pay_request_{$payRequestId}";
    }

    /**
     * Create a lock identifier for notification processing
     *
     * @param string $reference
     * @param string $payRequestId
     *
     * @return string
     */
    public function createNotifyLockIdentifier(string $reference, string $payRequestId): string
    {
        return "notify_{$reference}_pay_request_{$payRequestId}";
    }

    /**
     * Clean up expired locks
     *
     * @return void
     */
    public function cleanupExpiredLocks(): void
    {
        $this->cache->clean(['payhost_lock']);
        $this->logger->info("Cleaned up expired PayHost locks");
    }
}
