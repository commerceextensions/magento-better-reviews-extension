<?php

class CommerceExtensions_BetterReviews_Model_Catalog_Product_View_Observer
{
    /**
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function addHandle(Varien_Event_Observer $observer)
    {
        $allowedHandles = array('catalog_product_view','checkout_cart_configure');
        $currentHandles = Mage::helper('betterreviews')->getHandles();
        if(array_intersect($allowedHandles,$currentHandles)) {
            $product = Mage::registry('current_product');
            if($product instanceof Mage_Catalog_Model_Product) {
                /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
                $typeInstance = Mage::getSingleton('betterreviews/config')->getTypeInstance($product);
                if($typeInstance->getIsEnabled()) {
                    if($typeInstance->getShowReviewsOnProductPage()) {
                        if(!Mage::helper('betterreviews')->isProductPage()) {
                            return $this;
                        }
                        $update = $observer->getEvent()->getLayout()->getUpdate();
                        $update->addHandle('product_page_reviews');
                    }
                }
            }
        }
        return $this;
    }
}