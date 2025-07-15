<?php
/**
 * @category  Vivlavoni
 * @package   Vivlavoni/DisableLoginAndCreateAccount
 * @author    Volodymyr Blazhkov r2d2maloy98@gmail.com
 * @copyright 2025 Volodymyr Blazhkov internet solutions GmbH
 */
declare(strict_types=1);

namespace Vivlavoni\DisableLoginAndCreateAccount\Plugin;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Vivlavoni\DisableLoginAndCreateAccount\Model\Config\ConfigProvider;

class DisableLogin
{
    /**
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        private readonly ConfigProvider $configProvider
    ) {
    }

    /**
     * Intercepts the authentication process to apply additional checks before proceeding.
     *
     * @param AccountManagement $subject The instance of the AccountManagement being intercepted.
     * @param string $username The username provided during authentication.
     * @param string $password The password provided during authentication.
     * @return array Returns an array containing the username and password.
     * @throws InvalidEmailOrPasswordException Thrown if login is temporarily disabled based on the configuration.
     */
    public function beforeAuthenticate(AccountManagement $subject, $username, $password): array
    {
        if (!$this->configProvider->isEnabled()) {
            return [$username, $password];
        }

        if ($this->configProvider->isLoginEnabled()
            && $this->configProvider->detectAllLoginEmailsAllowed((string) $username)) {
            throw new InvalidEmailOrPasswordException(__('Login is temporarily disabled.'));
        }

        return [$username, $password];
    }
}
