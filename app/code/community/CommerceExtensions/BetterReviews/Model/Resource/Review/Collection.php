<?php

class CommerceExtensions_BetterReviews_Model_Resource_Review_Collection extends Mage_Review_Model_Resource_Review_Collection
{
    /**
     * @var null|int
     */
    protected $_storeId;

    public function addStoreFilter($storeId)
    {
        $this->_storeId = $storeId;
        return parent::addStoreFilter($storeId);
    }

    /**
     * If product is a composite product, we make sure to get the reviews for it and all of its associated products. Otherwise return native functionality
     *
     * @param int|string $entity
     * @param int        $pkValue
     *
     * @return $this|\Mage_Review_Model_Resource_Review_Collection
     */
    public function addEntityFilter($entity, $pkValue)
    {
        /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
        $typeInstance = Mage::getSingleton('betterreviews/config')->getTypeInstance($pkValue);
        if($typeInstance->getIsEnabled()) {
            if($entity == 'product' && $typeInstance->getCanIncludeAssociatedReviews()) {
                if (is_numeric($entity)) {
                    $this->addFilter('entity', $this->getConnection()->quoteInto('main_table.entity_id=?', $entity), 'string');
                } elseif (is_string($entity)) {
                    $this->_select->join($this->_reviewEntityTable, 'main_table.entity_id='.$this->_reviewEntityTable.'.entity_id', array('entity_code'));
                    $this->addFilter('entity', $this->getConnection()->quoteInto($this->_reviewEntityTable.'.entity_code=?', $entity), 'string');
                }

                /** @var CommerceExtensions_BetterReviews_Model_Helper $helper */
                $helper = Mage::getModel('betterreviews/helper');
                $helper->setStoreId($this->_storeId);
                $helper->setProductIdsFilter($pkValue);
                $helper->includeAssociatedProducts($typeInstance->getProductType($pkValue));

                $this->addFilter('entity_pk_value', $this->getConnection()->quoteInto('main_table.entity_pk_value IN(?)', $helper->getProductIds()), 'string');
                return $this;
            }
        }
        return parent::addEntityFilter($entity, $pkValue);
    }
}