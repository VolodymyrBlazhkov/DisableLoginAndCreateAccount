<?php
/**
 * @category  Vivlavoni
 * @package   Vivlavoni/DisableLoginAndCreateAccount
 * @author    Volodymyr Blazhkov r2d2maloy98@gmail.com
 * @copyright 2025 Volodymyr Blazhkov internet solutions GmbH
 */
declare(strict_types=1);

namespace Vivlavoni\DisableLoginAndCreateAccount\Setup\Patch\Data;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AddCustomerUseDisabledConfigAttribute implements DataPatchInterface
{
    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @var Config
     */
    private Config $eavConfig;

    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Applies the setup required to add the 'is_approved' customer attribute.
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Customer::ENTITY,
            'is_use_config_disabled_login',
            [
                'type' => 'int',
                'label' => 'Use Global config for Disable Login',
                'input' => 'boolean',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'required' => false,
                'default' => 1,
                'visible' => true,
                'user_defined' => true,
                'position' => 25,
                'system' => 0,
                'backend_type' => 'int',
                'frontend_input' => 'boolean',
                'is_used_in_grid' => 0,
                'is_visible_in_grid' => 0,
                'is_filterable_in_grid' => 0,
                'is_searchable_in_grid' => 0,
            ]
        );
        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            'is_use_config_disabled_login'
        );

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'is_use_config_disabled_login');
        $attribute->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $attribute->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Retrieves an array of class dependencies.
     *
     * @return array An empty array indicating no dependencies.
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Retrieves the list of aliases.
     *
     * @return array An array of aliases.
     */
    public function getAliases()
    {
        return [];
    }
}
