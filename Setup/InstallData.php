<?php
namespace Evato\Globis\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

 $extr_attr_arr = array('glob_article_status_id'=>'Glob Article Status Id','glob_sub_group_id'=>'Glob Subgroup Id','glob_article_type_id'=>'Glob Article Type Id','glob_article_code'=>'Glob Article Code','glob_article_status_code'=>'Glob Article Status','glob_article_type_code'=>'Glob Article Type','glob_ean_code'=>'Glob Ean Code','glob_featured_from'=>'Glob Featured From','glob_featured_until'=>'Glob Featured Until','glob_minimum_order_quantity'=>'Glob Minimum Order Quantity','glob_new_from'=>'Glob New From','glob_new_until'=>'Glob New Until','glob_quantity_multiple_of'=>'Glob Quantity Multiple ','glob_sub_group_code'=>'Glob Subgroup','glob_allow_in_webshop'=>'Glob Allow In Webshop','glob_expected_delivery_date'=>'Glob Expected Delivery Date','glob_min_delivery_in_days'=>'Glob Min Delivery In Days','glob_max_delivery_in_days'=>'Glob Max Delivery In Days' );
          
          foreach ($extr_attr_arr as $x => $y) {



        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $x,
            [
                'type' => 'int',
                'label' => $y,
                'input' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',             
                'visible_on_front' => false,
                'group' => 'GLOBIS Attributes',

                               
            ]
        );
    }
    }
}