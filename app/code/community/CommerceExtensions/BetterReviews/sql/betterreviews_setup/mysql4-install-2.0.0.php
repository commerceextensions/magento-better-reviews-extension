<?php

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->delete($installer->getTable('core/config_data'),array("path LIKE ?" => 'productpagereviews%'));
$installer->getConnection()->delete($installer->getTable('core/config_data'),array("path LIKE ?" => 'associatedproductreviews%'));

$installer->setConfigData('betterreviews/general/enabled',1);
$installer->setConfigData('betterreviews/simple_product/enabled',1);
$installer->setConfigData('betterreviews/simple_product/reviews_on_product_page',1);
$installer->setConfigData('betterreviews/simple_product/change_block_title',1);
$installer->setConfigData('betterreviews/simple_product/block_title_pattern','Reviews for {{name}}');
$installer->setConfigData('betterreviews/simple_product/list_order','length');
$installer->setConfigData('betterreviews/simple_product/list_dir',Varien_Db_Select::SQL_DESC);
$installer->setConfigData('betterreviews/simple_product/structured_data_enabled',1);
$installer->setConfigData('betterreviews/virtual_product/enabled',1);
$installer->setConfigData('betterreviews/virtual_product/reviews_on_product_page',1);
$installer->setConfigData('betterreviews/virtual_product/change_block_title',1);
$installer->setConfigData('betterreviews/virtual_product/block_title_pattern','Reviews for {{name}}');
$installer->setConfigData('betterreviews/virtual_product/list_order','length');
$installer->setConfigData('betterreviews/virtual_product/list_dir',Varien_Db_Select::SQL_DESC);
$installer->setConfigData('betterreviews/virtual_product/structured_data_enabled',1);
$installer->setConfigData('betterreviews/downloadable_product/enabled',1);
$installer->setConfigData('betterreviews/downloadable_product/reviews_on_product_page',1);
$installer->setConfigData('betterreviews/downloadable_product/change_block_title',1);
$installer->setConfigData('betterreviews/downloadable_product/block_title_pattern','Reviews for {{name}}');
$installer->setConfigData('betterreviews/downloadable_product/list_order','length');
$installer->setConfigData('betterreviews/downloadable_product/list_dir',Varien_Db_Select::SQL_DESC);
$installer->setConfigData('betterreviews/downloadable_product/structured_data_enabled',1);
$installer->setConfigData('betterreviews/grouped_product/enabled',1);
$installer->setConfigData('betterreviews/grouped_product/include_associated_product_reviews',1);
$installer->setConfigData('betterreviews/grouped_product/show_product_name',1);
$installer->setConfigData('betterreviews/grouped_product/reviews_on_product_page',1);
$installer->setConfigData('betterreviews/grouped_product/change_block_title',1);
$installer->setConfigData('betterreviews/grouped_product/block_title_pattern','Reviews for {{name}}');
$installer->setConfigData('betterreviews/grouped_product/list_order','length');
$installer->setConfigData('betterreviews/grouped_product/list_dir',Varien_Db_Select::SQL_DESC);
$installer->setConfigData('betterreviews/grouped_product/structured_data_enabled',1);
$installer->setConfigData('betterreviews/configurable_product/enabled',1);
$installer->setConfigData('betterreviews/configurable_product/include_associated_product_reviews',1);
$installer->setConfigData('betterreviews/configurable_product/show_product_name',1);
$installer->setConfigData('betterreviews/configurable_product/reviews_on_product_page',1);
$installer->setConfigData('betterreviews/configurable_product/change_block_title',1);
$installer->setConfigData('betterreviews/configurable_product/block_title_pattern','Reviews for {{name}}');
$installer->setConfigData('betterreviews/configurable_product/list_order','length');
$installer->setConfigData('betterreviews/configurable_product/list_dir',Varien_Db_Select::SQL_DESC);
$installer->setConfigData('betterreviews/configurable_product/structured_data_enabled',1);
$installer->setConfigData('betterreviews/bundle_product/enabled',1);
$installer->setConfigData('betterreviews/bundle_product/include_associated_product_reviews',1);
$installer->setConfigData('betterreviews/bundle_product/show_product_name',1);
$installer->setConfigData('betterreviews/bundle_product/reviews_on_product_page',1);
$installer->setConfigData('betterreviews/bundle_product/change_block_title',1);
$installer->setConfigData('betterreviews/bundle_product/block_title_pattern','Reviews for {{name}}');
$installer->setConfigData('betterreviews/bundle_product/list_order','length');
$installer->setConfigData('betterreviews/bundle_product/list_dir',Varien_Db_Select::SQL_DESC);
$installer->setConfigData('betterreviews/bundle_product/structured_data_enabled',1);

$installer->endSetup();