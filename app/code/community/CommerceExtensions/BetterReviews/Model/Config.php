<?php

class CommerceExtensions_BetterReviews_Model_Config extends Mage_Core_Model_Abstract
{
    const XML_PATH_ENABLED = 'betterreviews/general/enabled';

    /**
     * @var array
     */
    protected $_productTypes = array();

    /**
     * @var array
     */
    protected $_typeInstances = array();

    protected function _construct()
    {
        $this->setData('store_id', Mage::app()->getStore()->getId());
        $this->setConnection(Mage::getSingleton('core/resource')->getConnection('core_read'));
        parent::_construct();
    }

    /**
     * @return \CommerceExtensions_BetterReviews_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('betterreviews');
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        if(!$this->hasData('module_enabled')) {
            if(!$this->_getHelper()->isAdminArea()) {
                $this->setData('module_enabled',(bool)Mage::getStoreConfig(self::XML_PATH_ENABLED, $this->getStoreId()));
            } else {
                $this->setData('module_enabled',false);
            }
        }
        return $this->getData('module_enabled');
    }

    /**
     * @param int|\Mage_Catalog_Model_Product $product
     *
     * @return int
     */
    public function getProductId($product)
    {
        if($product instanceof Mage_Catalog_Model_Product) {
            return $product->getId();
        }
        return (int)$product;
    }

    /**
     * @param int|\Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public function getProductType($product)
    {
        if(empty($this->_productTypes)) {
            $adapter             = $this->getConnection();
            $select              = $adapter->select()->from(Mage::helper('cecore/db')->getTableName('catalog_product_entity'), array('entity_id', 'type_id'));
            $this->_productTypes = $adapter->fetchPairs($select);
        }
        $productId = $this->getProductId($product);
        return array_key_exists($productId,$this->_productTypes) ? $this->_productTypes[$productId] : null;
    }

    /**
     * @param int|\Mage_Catalog_Model_Product $product
     *
     * @return \CommerceExtensions_BetterReviews_Model_Config_Product_Type_Bundle|\CommerceExtensions_BetterReviews_Model_Config_Product_Type_Configurable|\CommerceExtensions_BetterReviews_Model_Config_Product_Type_Downloadable|\CommerceExtensions_BetterReviews_Model_Config_Product_Type_Grouped|\CommerceExtensions_BetterReviews_Model_Config_Product_Type_Simple|\CommerceExtensions_BetterReviews_Model_Config_Product_Type_Virtual
     */
    public function getTypeInstance($product)
    {
        $productId = $this->getProductId($product);
        if(!array_key_exists($productId, $this->_typeInstances)) {
            $type  = $this->getProductType($product);
            $model = Mage::getModel("betterreviews/config_product_type_{$type}");
            $model->setStoreId($this->getStoreId());
            $this->_typeInstances[$productId] = $model;
        }
        return $this->_typeInstances[$productId];
    }
}