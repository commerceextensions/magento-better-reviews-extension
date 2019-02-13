<?php

class CommerceExtensions_BetterReviews_Model_Resource_Review extends Mage_Review_Model_Resource_Review
{
    /**
     * Retrieves total reviews
     *
     * @param int  $entityPkValue
     * @param bool $approvedOnly
     * @param int  $storeId
     *
     * @return int
     */
    public function getTotalReviews($entityPkValue, $approvedOnly = false, $storeId = 0)
    {
        /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
        $typeInstance = Mage::getSingleton('betterreviews/config')->getTypeInstance($entityPkValue);
        if($typeInstance->getIsEnabled()) {
            if($typeInstance->getCanIncludeAssociatedReviews()){

                /** @var CommerceExtensions_BetterReviews_Model_Helper $helper */
                $helper = Mage::getModel('betterreviews/helper');
                $helper->setStoreId($storeId);
                $helper->setProductIdsFilter($entityPkValue);
                $helper->includeAssociatedProducts($typeInstance->getProductType($entityPkValue));

                $adapter = $this->_getReadAdapter();
                $select  = $adapter->select()->from($this->_reviewTable, array('review_count' => new Zend_Db_Expr('COUNT(*)')));
                $select->where("{$this->_reviewTable}.entity_pk_value IN(?)", $helper->getProductIdsSelect());

                if($storeId > 0) {
                    $select->join(array('store' => $this->_reviewStoreTable),
                                  $this->_reviewTable . '.review_id=store.review_id AND store.store_id = :store_id',
                                  array());
                    $bind[':store_id'] = (int)$storeId;
                }
                if($approvedOnly) {
                    $select->where("{$this->_reviewTable}.status_id = :status_id");
                    $bind[':status_id'] = Mage_Review_Model_Review::STATUS_APPROVED;
                }
                return $adapter->fetchOne($select, $bind);
            }
        }
        return parent::getTotalReviews($entityPkValue, $approvedOnly, $storeId);
    }
}