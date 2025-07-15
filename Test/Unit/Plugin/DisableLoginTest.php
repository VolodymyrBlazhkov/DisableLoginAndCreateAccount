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

class DisableLoginTest extends TestCase
{
    /**
     * @var DisableLogin
     */
    private DisableLogin $plugin;

    /**
     * @var ConfigProvider
     */
    private ConfigProvider $configProviderMock;

    protected function setUp(): void
    {
        $this->configProviderMock = $this->createMock(ConfigProvider::class);
        $this->plugin = new DisableLogin($this->configProviderMock);
    }

    public function testBeforeAuthenticateWhenFeatureDisabled(): void
    {
        $this->configProviderMock->method('isEnabled')->willReturn(false);

        $accountManagementMock = $this->createMock(AccountManagement::class);

        $username = 'test@example.com';
        $password = 'password';

        $result = $this->plugin->beforeAuthenticate($accountManagementMock, $username, $password);

        $this->assertSame([$username, $password], $result);
    }

    public function testBeforeAuthenticateWhenLoginEnabledAndEmailNotAllowed(): void
    {
        $this->configProviderMock->method('isEnabled')->willReturn(true);
        $this->configProviderMock->method('isLoginEnabled')->willReturn(true);
        $this->configProviderMock->method('detectAllLoginEmailsAllowed')->willReturn(false);

        $accountManagementMock = $this->createMock(AccountManagement::class);

        $username = 'test@example.com';
        $password = 'password';

        $result = $this->plugin->beforeAuthenticate($accountManagementMock, $username, $password);

        $this->assertSame([$username, $password], $result);
    }

    public function testBeforeAuthenticateWhenLoginEnabledAndEmailAllowed(): void
    {
        $this->configProviderMock->method('isEnabled')->willReturn(true);
        $this->configProviderMock->method('isLoginEnabled')->willReturn(true);
        $this->configProviderMock->method('detectAllLoginEmailsAllowed')->willReturn(true);

        $accountManagementMock = $this->createMock(AccountManagement::class);

        $username = 'test@example.com';
        $password = 'password';

        $this->expectException(InvalidEmailOrPasswordException::class);
        $this->expectExceptionMessage('Login is temporarily disabled.');

        $this->plugin->beforeAuthenticate($accountManagementMock, $username, $password);
    }

    public function testBeforeAuthenticateWhenLoginDisabled(): void
    {
        $this->configProviderMock->method('isEnabled')->willReturn(true);
        $this->configProviderMock->method('isLoginEnabled')->willReturn(false);

        $accountManagementMock = $this->createMock(AccountManagement::class);

        $username = 'test@example.com';
        $password = 'password';

        $result = $this->plugin->beforeAuthenticate($accountManagementMock, $username, $password);

        $this->assertSame([$username, $password], $result);
    }
}
