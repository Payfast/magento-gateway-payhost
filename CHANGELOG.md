# Changelog

## [[1.2.0]](https://github.com/Payfast/magento-gateway-payhost/releases/tag/v1.2.0)

### Fixed

- **Duplicate invoice creation and order confirmation emails** caused by multiple / simultaneous PayHost notification
  requests  
  (most common in high-latency or retry-heavy scenarios).

### Added

- Cache-based **locking** to ensure only one notification is processed per order.
- **Duplicate email prevention** (tracks & blocks repeated confirmation/invoice emails).
- Daily cron job that cleans up expired locks and email tracking records.

### Changed

- Refactored `Notify/Index` controller:
    - Acquires lock before processing.
    - Skips if lock cannot be obtained.
    - Cleaner structure with better error handling & logging.
    - Applies duplicate-email check before sending any email.

- This release makes notification handling idempotent and reliable under concurrent / retry conditions.

## [[1.1.0]](https://github.com/Payfast/magento-gateway-payhost/releases/tag/v1.1.0)

### Added

- Compatibility update for Magento 2.4.7 and PHP 8.2.
- Upgraded Guzzle HTTP client for improved performance and compatibility.
- Add **Disable IPN** configuration option.

### Fixed

- Issues with card vaulting and tokenized payments on some configurations.
- Query method for **Cron** and **Fetch** reliability.
- IPN/Redirect method reliability.

## [[1.0.1]](https://github.com/Payfast/magento-gateway-payhost/releases/tag/v1.0.1)

### Added

- Tested compatibility with Magento 2.4.6.
- Display Payhost SOAP error notifications.

### Changed

- Refactored code to comply with Magento 2 PHP coding standards.

### Fixed

- General bug fixes and improvements.

## [[1.0.0]](https://github.com/Payfast/magento-gateway-payhost/releases/tag/v1.0.0)

### Added

- Initial version release.
