<?php

require_once(Mage::getModuleDir('controllers','Mage_Review').DS.'ProductController.php');
class CommerceExtensions_BetterReviews_ProductController extends Mage_Review_ProductController
{
    protected function _loadProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')
                       ->setStoreId(Mage::app()->getStore()->getId())
                       ->load($productId);

        /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
        $typeInstance = Mage::getSingleton('betterreviews/config')->getTypeInstance($product);
        if($typeInstance->getIsEnabled()) {
            Mage::register('current_product', $product);
            Mage::register('product', $product);
            return $product;
        }
        return parent::_loadProduct($productId);
    }
}