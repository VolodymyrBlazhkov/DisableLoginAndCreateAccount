<?php
/**
 * @category  Vivlavoni
 * @package   Vivlavoni/DisableLoginAndCreateAccount
 * @author    Volodymyr Blazhkov r2d2maloy98@gmail.com
 * @copyright 2025 Volodymyr Blazhkov internet solutions GmbH
 */
declare(strict_types=1);

namespace Vivlavoni\DisableLoginAndCreateAccount\Test\Unit\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vivlavoni\DisableLoginAndCreateAccount\Model\Config\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->configProvider = new ConfigProvider($this->scopeConfigMock);
    }

    public function testIsCreateAccountEnabledReturnsTrueWhenEnabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/is_create_enabled', ScopeInterface::SCOPE_STORE)
            ->willReturn(true);

        $result = $this->configProvider->isCreateAccountEnabled();

        $this->assertTrue($result);
    }

    public function testIsCreateAccountEnabledReturnsFalseWhenDisabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/is_create_enabled', ScopeInterface::SCOPE_STORE)
            ->willReturn(false);

        $result = $this->configProvider->isCreateAccountEnabled();

        $this->assertFalse($result);
    }

    public function testDetectAllCreateAccountEmailsAllowedReturnsFalseIfEmailIsAllowed(): void
    {
        $allowedEmails = "allowed@example.com\nallowed2@example.com";
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_create_emails_allowed')
            ->willReturn($allowedEmails);

        $result = $this->configProvider->detectAllCreateAccountEmailsAllowed('allowed@example.com');

        $this->assertFalse($result);
    }

    public function testDetectAllLoginEmailsAllowedReturnsFalseIfEmailIsAllowed(): void
    {
        $allowedEmails = "allowed@example.com\nallowed2@example.com";
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_login_emails_allowed')
            ->willReturn($allowedEmails);

        $result = $this->configProvider->detectAllLoginEmailsAllowed('allowed@example.com');

        $this->assertFalse($result);
    }

    public function testDetectAllLoginEmailsAllowedReturnsTrueIfEmailIsNotAllowed(): void
    {
        $allowedEmails = "allowed@example.com\nallowed2@example.com";
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_login_emails_allowed')
            ->willReturn($allowedEmails);

        $result = $this->configProvider->detectAllLoginEmailsAllowed('notallowed@example.com');

        $this->assertTrue($result);
    }

    public function testDetectAllLoginEmailsAllowedReturnsTrueIfAllowedEmailsListIsEmpty(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_login_emails_allowed')
            ->willReturn(null);

        $result = $this->configProvider->detectAllLoginEmailsAllowed('test@example.com');

        $this->assertTrue($result);
    }

    public function testDetectAllCreateAccountEmailsAllowedReturnsTrueIfEmailIsNotAllowed(): void
    {
        $allowedEmails = "allowed@example.com\nallowed2@example.com";
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_create_emails_allowed')
            ->willReturn($allowedEmails);

        $result = $this->configProvider->detectAllCreateAccountEmailsAllowed('notallowed@example.com');

        $this->assertTrue($result);
    }

    public function testDetectAllCreateAccountEmailsAllowedReturnsTrueIfAllowedEmailsListIsEmpty(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_create_emails_allowed')
            ->willReturn(null);

        $result = $this->configProvider->detectAllCreateAccountEmailsAllowed('test@example.com');

        $this->assertTrue($result);
    }

    public function testGetCreateAccountEmailsAllowedReturnsEmptyArrayIfConfigValueIsNull(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_create_emails_allowed')
            ->willReturn(null);

        $result = $this->configProvider->getCreateAccountEmailsAllowed();

        $this->assertSame([], $result);
    }

    public function testGetCreateAccountEmailsAllowedParsesConfigValue(): void
    {
        $emails = "allowed@example.com\nallowed2@example.com";
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_create_emails_allowed')
            ->willReturn($emails);

        $result = $this->configProvider->getCreateAccountEmailsAllowed();

        $this->assertSame(['allowed@example.com', 'allowed2@example.com'], $result);
    }

    public function testGetCreateAccountEmailsAllowedTrimsAndIgnoresEmptyValues(): void
    {
        $emails = " allowed@example.com\nALLOWED2@EXAMPLE.COM \n";
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_create_emails_allowed')
            ->willReturn($emails);

        $result = $this->configProvider->getCreateAccountEmailsAllowed();

        $this->assertSame(['allowed@example.com', 'allowed2@example.com'], $result);
    }

    public function testGetLoginEmailsAllowedReturnsEmptyArrayIfConfigValueIsNull(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_login_emails_allowed')
            ->willReturn(null);

        $result = $this->configProvider->getloginEmailsAllowed();

        $this->assertSame([], $result);
    }

    public function testGetLoginEmailsAllowedParsesConfigValue(): void
    {
        $emails = "allowed@example.com\nallowed2@example.com";
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_login_emails_allowed')
            ->willReturn($emails);

        $result = $this->configProvider->getloginEmailsAllowed();

        $this->assertSame(['allowed@example.com', 'allowed2@example.com'], $result);
    }

    public function testGetLoginEmailsAllowedTrimsAndIgnoresEmptyValues(): void
    {
        $emails = " allowed@example.com\nALLOWED2@EXAMPLE.COM \n";
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/destination_login_emails_allowed')
            ->willReturn($emails);

        $result = $this->configProvider->getloginEmailsAllowed();

        $this->assertSame(['allowed@example.com', 'allowed2@example.com'], $result);
    }
    public function testIsLoginEnabledReturnsTrueWhenEnabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/is_login_enabled', ScopeInterface::SCOPE_STORE)
            ->willReturn(true);

        $result = $this->configProvider->isLoginEnabled();

        $this->assertTrue($result);
    }

    public function testIsLoginEnabledReturnsFalseWhenDisabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/is_login_enabled', ScopeInterface::SCOPE_STORE)
            ->willReturn(false);

        $result = $this->configProvider->isLoginEnabled();

        $this->assertFalse($result);
    }

    public function testIsEnabledReturnsTrueWhenEnabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/is_enabled', ScopeInterface::SCOPE_STORE)
            ->willReturn(true);

        $result = $this->configProvider->isEnabled();

        $this->assertTrue($result);
    }

    public function testIsEnabledReturnsFalseWhenDisabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with('vivlavoni_customer/customer/is_enabled', ScopeInterface::SCOPE_STORE)
            ->willReturn(false);

        $result = $this->configProvider->isEnabled();

        $this->assertFalse($result);
    }
}
