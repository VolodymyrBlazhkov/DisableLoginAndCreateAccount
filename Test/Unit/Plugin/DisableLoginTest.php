<?php
/**
 * @category  Vivlavoni
 * @package   Vivlavoni/DisableLoginAndCreateAccount
 * @author    Volodymyr Blazhkov r2d2maloy98@gmail.com
 * @copyright 2025 Volodymyr Blazhkov internet solutions GmbH
 */
declare(strict_types=1);

namespace Vivlavoni\DisableLoginAndCreateAccount\Test\Unit\Plugin;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use PHPUnit\Framework\TestCase;
use Vivlavoni\DisableLoginAndCreateAccount\Model\Config\ConfigProvider;
use Vivlavoni\DisableLoginAndCreateAccount\Plugin\DisableLogin;
use Magento\Customer\Api\CustomerRepositoryInterface;

class DisableLoginTest extends TestCase
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AccountManagement
     */
    private $accountManagement;

    /**
     * @var DisableLogin
     */
    private $disableLogin;

    protected function setUp(): void
    {
        $this->configProvider = $this->createMock(ConfigProvider::class);
        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->accountManagement = $this->createMock(AccountManagement::class);
        $this->disableLogin = new DisableLogin($this->configProvider, $this->customerRepository);
    }

    public function testBeforeAuthenticateLoginDisabled(): void
    {
        $this->configProvider
            ->method('isEnabled')
            ->willReturn(false);

        $this->configProvider
            ->expects($this->never())
            ->method('isLoginEnabled');

        $result = $this->disableLogin->beforeAuthenticate(
            $this->accountManagement,
            'test@example.com',
            'password123'
        );

        $this->assertSame(['test@example.com', 'password123'], $result);
    }

    public function testBeforeAuthenticateThrowsExceptionWhenLoginDisabledByCustomerConfig(): void
    {
        $customerMock = $this->createMock(\Magento\Customer\Api\Data\CustomerInterface::class);
        $attributeMock = $this->createMock(\Magento\Framework\Api\AttributeInterface::class);

        $this->configProvider
            ->method('isEnabled')
            ->willReturn(true);

        $this->configProvider
            ->method('isLoginEnabled')
            ->willReturn(true);

        $customerMock
            ->method('getCustomAttribute')
            ->with('is_use_config_disabled_login')
            ->willReturn($attributeMock);

        $attributeMock
            ->method('getValue')
            ->willReturn(true);

        $this->customerRepository
            ->method('get')
            ->with('test@example.com')
            ->willReturn($customerMock);

        $this->configProvider
            ->method('detectAllLoginEmailsAllowed')
            ->with('test@example.com')
            ->willReturn(true);

        $this->expectException(\Magento\Framework\Exception\InvalidEmailOrPasswordException::class);
        $this->expectExceptionMessage('Login is temporarily disabled.');

        $this->disableLogin->beforeAuthenticate(
            $this->accountManagement,
            'test@example.com',
            'password123'
        );
    }

    public function testBeforeAuthenticateThrowsExceptionWhenAllEmailsRestricted(): void
    {
        $this->configProvider
            ->method('isEnabled')
            ->willReturn(true);

        $this->configProvider
            ->method('isLoginEnabled')
            ->willReturn(true);

        $this->customerRepository
            ->method('get')
            ->with('test@example.com')
            ->willThrowException(new \Exception('No such customer'));

        $this->configProvider
            ->method('detectAllLoginEmailsAllowed')
            ->with('test@example.com')
            ->willReturn(true);

        $this->expectException(\Magento\Framework\Exception\InvalidEmailOrPasswordException::class);
        $this->expectExceptionMessage('Login is temporarily disabled.');

        $this->disableLogin->beforeAuthenticate(
            $this->accountManagement,
            'test@example.com',
            'password123'
        );
    }
}
