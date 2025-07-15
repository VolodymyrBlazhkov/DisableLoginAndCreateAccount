<?php
/**
 * @category  Vivlavoni
 * @package   Vivlavoni/DisableLoginAndCreateAccount
 * @author    Volodymyr Blazhkov r2d2maloy98@gmail.com
 * @copyright 2025 Volodymyr Blazhkov internet solutions GmbH
 */
declare(strict_types=1);

namespace Vivlavoni\DisableLoginAndCreateAccount\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Provides access to configuration values defined in the application scope.
 */
class ConfigProvider
{
    private const IS_ENABLED = 'vivlavoni_customer/customer/is_enabled';

    private const IS_LOGIN_ENABLED = 'vivlavoni_customer/customer/is_login_enabled';
    private const DESTINATION_LOGIN_EMAILS_ALLOWED = 'vivlavoni_customer/customer/destination_login_emails_allowed';
    private const IS_CREATE_ENABLED = 'vivlavoni_customer/customer/is_create_enabled';
    private const DESTINATION_CREATE_EMAILS_ALLOWED = 'vivlavoni_customer/customer/destination_create_emails_allowed';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Checks if enabled in the scope configuration.
     *
     * @return bool True if the Stock feature is enabled, false otherwise.
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if login enabled in the scope configuration.
     *
     * @return bool True if the Stock feature is enabled, false otherwise.
     */
    public function isLoginEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::IS_LOGIN_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if create account enabled in the scope configuration.
     *
     * @return bool True if the Stock feature is enabled, false otherwise.
     */
    public function isCreateAccountEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::IS_CREATE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieves the list of allowed login email from the scope configuration.
     *
     * @return array Array of allowed login email addresses, excluding empty values.
     */
    public function getloginEmailsAllowed(): array
    {
        $emails = $this->scopeConfig->getValue(self::DESTINATION_LOGIN_EMAILS_ALLOWED);
        if ($emails === null) {
            return [];
        }
        $emails = array_map('trim', explode("\n", $emails));
        $emails = array_map('strtolower', $emails);
        $emails = array_filter($emails, function ($value) {
            return !empty($value);
        });
        return $emails;
    }

    /**
     * Retrieves the list of allowed login email from the scope configuration.
     *
     * @return array Array of allowed login email addresses, excluding empty values.
     */
    public function getCreateAccountEmailsAllowed(): array
    {
        $emails = $this->scopeConfig->getValue(self::DESTINATION_CREATE_EMAILS_ALLOWED);
        if ($emails === null) {
            return [];
        }
        $emails = array_map('trim', explode("\n", $emails));
        $emails = array_map('strtolower', $emails);
        $emails = array_filter($emails, function ($value) {
            return !empty($value);
        });
        return $emails;
    }

    /**
     * Determines if the given email is allowed for login based on the destination email rules.
     *
     * @param string $email The email address to check.
     * @return bool True if the email is allowed for login, false otherwise.
     */
    public function detectAllLoginEmailsAllowed(string $email): bool
    {
        return !in_array(strtolower($email), $this->getloginEmailsAllowed());
    }

    /**
     * Determines if the provided email is allowed for creating an account.
     *
     * @param string $email The email address to check against the allowed list.
     * @return bool True if the email is in the allowed list, false otherwise.
     */
    public function detectAllCreateAccountEmailsAllowed(string $email): bool
    {
        return !in_array(strtolower($email), $this->getCreateAccountEmailsAllowed());
    }
}
