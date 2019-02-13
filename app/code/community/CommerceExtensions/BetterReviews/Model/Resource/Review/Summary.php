<?php

class CommerceExtensions_BetterReviews_Model_Resource_Review_Summary extends Mage_Review_Model_Resource_Review_Summary
{
    /**
     * @var \Varien_Db_Select
     */
    protected $_fullSummarySelect;

    /**
     * @param \Mage_Core_Model_Abstract $object
     * @param mixed                     $value
     * @param null                      $field
     *
     * @return \Mage_Core_Model_Resource_Db_Abstract
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if($field == $this->getIdFieldName() || is_null($field)){
            /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
            $typeInstance = Mage::getSingleton('betterreviews/config')->getTypeInstance($value);
            if($typeInstance->getIsEnabled()) {
                if($typeInstance->getCanIncludeAssociatedReviews()) {
                    /** @var CommerceExtensions_BetterReviews_Model_Helper $helper */
                    $helper = Mage::getModel('betterreviews/helper');
                    $helper->setStoreId((int)$object->getStoreId());
                    $helper->setProductIdsFilter($value);
                    $helper->includeAssociatedProducts($typeInstance->getProductType($value));
                    $this->_fullSummarySelect = $helper->getFullSummarySelect();
                }
            }
        }
        return parent::load($object, $value, $field);
    }

    /**
     * @param \Mage_Core_Model_Abstract $object
     *
     * @return \Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if($this->_fullSummarySelect){
            $adapter = $this->_getReadAdapter();
            $summary = $adapter->fetchAll($this->_fullSummarySelect);
            if(array_key_exists(0,$summary)){
                $object->addData($summary[0]);
                $object->setData('entity_type',Mage::getModel('betterreviews/helper')->getReviewProductEntityType());
            }
        }
        return parent::_afterLoad($object);
    }
}