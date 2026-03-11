<?php

/*
 * Copyright (c) 2026 Payfast (Pty) Ltd
 *
 * Author: App Inlet (Pty) Ltd
 *
 * Released under the GNU General Public License
 */

namespace PayGate\PayHost\Cron;

use PayGate\PayHost\Helper\Lock;
use PayGate\PayHost\Helper\EmailDuplicatePrevention;
use Psr\Log\LoggerInterface;

/**
 * Cron job to clean up expired locks and email tracking entries
 */
class CleanupCache
{
    /**
     * @var Lock
     */
    private $lockHelper;

    /**
     * @var EmailDuplicatePrevention
     */
    private $emailDuplicateHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Lock $lockHelper
     * @param EmailDuplicatePrevention $emailDuplicateHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Lock $lockHelper,
        EmailDuplicatePrevention $emailDuplicateHelper,
        LoggerInterface $logger
    ) {
        $this->lockHelper           = $lockHelper;
        $this->emailDuplicateHelper = $emailDuplicateHelper;
        $this->logger               = $logger;
    }

    /**
     * Clean up expired locks and email tracking entries
     *
     * @return void
     */
    public function execute(): void
    {
        try {
            $this->logger->info('PayHost cache cleanup started');

            // Clean up expired locks
            $this->lockHelper->cleanupExpiredLocks();

            // Clean up expired email tracking entries
            $this->emailDuplicateHelper->cleanupExpiredEmailTracking();

            $this->logger->info('PayHost cache cleanup completed');
        } catch (\Exception $e) {
            $this->logger->error('PayHost cache cleanup failed: ' . $e->getMessage());
        }
    }
}
