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
use Magento\Customer\Api\CustomerRepositoryInterface;
use Vivlavoni\DisableLoginAndCreateAccount\Model\Config\ConfigProvider;

class DisableLogin
{
    /**
     * @param ConfigProvider $configProvider
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly CustomerRepositoryInterface $customerRepository
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

        $customer = $this->getCustomerByEmail((string) $username);

        if ($this->configProvider->isLoginEnabled()) {
            if ($customer && $customer->getCustomAttribute('is_use_config_disabled_login')) {
                $useConfig = (bool )$customer->getCustomAttribute('is_use_config_disabled_login')
                    ->getValue();

                if ($useConfig && $this->configProvider->detectAllLoginEmailsAllowed((string) $username)) {
                        throw new InvalidEmailOrPasswordException(__('Login is temporarily disabled.'));
                } else {
                    if ($customer->getCustomAttribute('is_disabled_login')
                        && $customer->getCustomAttribute('is_disabled_login')->getValue()) {
                        throw new InvalidEmailOrPasswordException(__('Login is temporarily disabled.'));
                    }
                }
            } else {
                if ($this->configProvider->detectAllLoginEmailsAllowed((string) $username)) {
                    throw new InvalidEmailOrPasswordException(__('Login is temporarily disabled.'));
                }
            }
        }

        return [$username, $password];
    }

    /**
     * Retrieves a customer by their email address.
     *
     * @param string $email The email address of the customer to retrieve.
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    private function getCustomerByEmail(string $email): ?\Magento\Customer\Api\Data\CustomerInterface
    {
        try {
            $customer = $this->customerRepository->get($email);
        } catch (\Exception $exception) {
            return null;
        }

        return $customer;
    }
}
