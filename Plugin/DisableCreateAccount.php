<?php
/**
 * @category  Vivlavoni
 * @package   Vivlavoni/DisableLoginAndCreateAccount
 * @author    Volodymyr Blazhkov r2d2maloy98@gmail.com
 * @copyright 2025 Volodymyr Blazhkov internet solutions GmbH
 */
declare(strict_types=1);

namespace Vivlavoni\DisableLoginAndCreateAccount\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\LocalizedException;
use Vivlavoni\DisableLoginAndCreateAccount\Model\Config\ConfigProvider;

class DisableCreateAccount
{
    /**
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        private readonly ConfigProvider $configProvider
    ) {
    }

    /**
     * Intercepts the account creation process
     *
     * @param AccountManagement $subject The account management object being intercepted.
     * @param CustomerInterface $customer The customer object containing account details.
     * @param string|null $password The customer's password, default is null if not provided.
     * @param string $redirectUrl The redirect URL after account creation, default is an empty string.
     * @return array The processed arguments [$customer, $password, $redirectUrl].
     * @throws LocalizedException If account creation is temporarily disabled for the provided email.
     */
    public function beforeCreateAccount(
        AccountManagement $subject,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ): array {
        if (!$this->configProvider->isEnabled()) {
            return [$customer, $password, $redirectUrl];
        }

        $email = $customer->getEmail();
        if ($this->configProvider->isCreateAccountEnabled()
            && $this->configProvider->detectAllCreateAccountEmailsAllowed((string) $email)) {
            throw new LocalizedException(
                __("Create Account is temporarily disabled.")
            );
        }

        return [$customer, $password, $redirectUrl];
    }
}
