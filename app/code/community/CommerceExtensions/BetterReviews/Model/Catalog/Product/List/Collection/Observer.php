<?php

class CommerceExtensions_BetterReviews_Model_Catalog_Product_List_Collection_Observer
{
    /**
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function addRatingSummary(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $observer->getEvent()->getCollection();
        $storeId = $collection->getStoreId();

        /** @var CommerceExtensions_BetterReviews_Model_Config $config */
        $config = Mage::getSingleton('betterreviews/config');
        $config->setStoreId($storeId);
        if($config->getIsEnabled()) {
            $helper = $this->_prepareHelper($collection->getAllIds(),$storeId);
            $summaries = $helper->getFullSummary();
            foreach($collection as &$product) {
                if(array_key_exists($product->getId(),$summaries)){
                    $summary = new Varien_Object($summaries[$product->getId()]);
                    $product->setRatingSummary($summary);
                }
            }
        }
        return $this;
    }

    /**
     * @param null|int|array $entityId
     * @param null|int $storeId
     *
     * @return \CommerceExtensions_BetterReviews_Model_Helper
     */
    protected function _prepareHelper($entityId = null,$storeId = null)
    {
        /** @var CommerceExtensions_BetterReviews_Model_Helper $helper */
        $helper = Mage::getModel('betterreviews/helper');
        $helper->setStoreId($storeId);
        $helper->setProductIdsFilter($entityId);

        /** @var CommerceExtensions_BetterReviews_Model_Config $config */
        $config = Mage::getSingleton('betterreviews/config');
        $config->setStoreId($storeId);

        if(is_array($entityId)) {
            foreach($entityId as $id) {
                /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
                $typeInstance = $config->getTypeInstance($id);
                if($typeInstance->getIsEnabled()) {
                    if($typeInstance->getCanIncludeAssociatedReviews()) {
                        $helper->includeAssociatedProducts($typeInstance->getProductType($id));
                    }
                }
            }
        } elseif(is_numeric($entityId)) {
            /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
            $typeInstance = $config->getTypeInstance($entityId);
            if($typeInstance->getIsEnabled()) {
                if($typeInstance->getCanIncludeAssociatedReviews()) {
                    $helper->includeAssociatedProducts($typeInstance->getProductType($entityId));
                }
            }
        } elseif(!$entityId) {
            /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Type_Grouped $typeInstance */
            $typeInstance = Mage::getModel('betterreviews/config_product_type_grouped');
            if($typeInstance->getIsEnabled()) {
                if($typeInstance->getCanIncludeAssociatedReviews()) {
                    $helper->includeAssociatedProducts(Mage_Catalog_Model_Product_Type::TYPE_GROUPED);
                }
            }

            /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Type_Configurable $typeInstance */
            $typeInstance = Mage::getModel('betterreviews/config_product_type_configurable');
            if($typeInstance->getIsEnabled()) {
                if($typeInstance->getCanIncludeAssociatedReviews()) {
                    $helper->includeAssociatedProducts(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
                }
            }

            /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Type_Bundle $typeInstance */
            $typeInstance = Mage::getModel('betterreviews/config_product_type_bundle');
            if($typeInstance->getIsEnabled()) {
                if($typeInstance->getCanIncludeAssociatedReviews()) {
                    $helper->includeAssociatedProducts(Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);
                }
            }
        }
        return $helper;
    }
}