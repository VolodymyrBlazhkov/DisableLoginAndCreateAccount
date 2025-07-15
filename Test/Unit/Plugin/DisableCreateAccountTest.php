<?php
/**
 * @category  Vivlavoni
 * @package   Vivlavoni/DisableLoginAndCreateAccount
 * @author    Volodymyr Blazhkov r2d2maloy98@gmail.com
 * @copyright 2025 Volodymyr Blazhkov internet solutions GmbH
 */
declare(strict_types=1);

namespace Vivlavoni\DisableLoginAndCreateAccount\Test\Unit\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Vivlavoni\DisableLoginAndCreateAccount\Model\Config\ConfigProvider;
use Vivlavoni\DisableLoginAndCreateAccount\Plugin\DisableCreateAccount;

class DisableCreateAccountTest extends TestCase
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @var DisableCreateAccount
     */
    private $plugin;

    protected function setUp(): void
    {
        $this->configProvider = $this->createMock(ConfigProvider::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->plugin = new DisableCreateAccount($this->configProvider);
    }

    public function testBeforeCreateAccountWhenDisabled(): void
    {
        $this->configProvider->method('isEnabled')->willReturn(false);

        $accountManagement = $this->createMock(AccountManagement::class);

        $args = [
            $this->customer,
            'testpassword',
            'https://redirect.url'
        ];

        $this->customer->expects($this->never())
            ->method('getEmail');

        $result = $this->plugin->beforeCreateAccount(
            $accountManagement,
            ...$args
        );

        $this->assertEquals($args, $result);
    }

    public function testBeforeCreateAccountThrowsException(): void
    {
        $this->configProvider->method('isEnabled')->willReturn(true);
        $this->configProvider->method('isCreateAccountEnabled')->willReturn(true);
        $this->configProvider->method('detectAllCreateAccountEmailsAllowed')->willReturn(true);

        $this->customer->method('getEmail')->willReturn('test@example.com');

        $accountManagement = $this->createMock(AccountManagement::class);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Create Account is temporarily disabled.');

        $this->plugin->beforeCreateAccount(
            $accountManagement,
            $this->customer,
            null,
            ''
        );
    }
}
