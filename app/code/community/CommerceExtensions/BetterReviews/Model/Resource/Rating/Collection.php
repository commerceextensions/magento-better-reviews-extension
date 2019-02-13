<?php

class CommerceExtensions_BetterReviews_Model_Resource_Rating_Collection extends Mage_Rating_Model_Resource_Rating_Collection
{
    /**
     * If product is composite, we include its associated products as well. Otherwise return native functionality
     *
     * @param int $entityPkValue
     * @param int $storeId
     *
     * @return \Mage_Rating_Model_Resource_Rating_Collection
     */
    public function addEntitySummaryToItem($entityPkValue, $storeId)
    {
        /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
        $typeInstance = Mage::getSingleton('betterreviews/config')->getTypeInstance($entityPkValue);
        if($typeInstance->getIsEnabled()) {
            if($typeInstance->getCanIncludeAssociatedReviews()) {

                /** @var CommerceExtensions_BetterReviews_Model_Helper $helper */
                $helper = Mage::getModel('betterreviews/helper');
                $helper->setStoreId($storeId);
                $helper->setProductIdsFilter($entityPkValue);
                $helper->includeAssociatedProducts($typeInstance->getProductType($entityPkValue));

                $arrRatingId = $this->getColumnValues('rating_id');
                if(count($arrRatingId) == 0) {
                    return $this;
                }

                $adapter = $this->getConnection();

                $inCond    = $adapter->prepareSqlCondition('rating_option_vote.rating_id', array(
                    'in' => $arrRatingId
                ));
                $sumCond   = new Zend_Db_Expr("SUM(rating_option_vote.{$adapter->quoteIdentifier('percent')})");
                $countCond = new Zend_Db_Expr('COUNT(*)');
                $select    = $adapter->select()
                                     ->from(array('rating_option_vote' => $this->getTable('rating/rating_option_vote')),
                                            array(
                                                'rating_id' => 'rating_option_vote.rating_id',
                                                'sum'       => $sumCond,
                                                'count'     => $countCond
                                            ))
                                     ->join(
                                         array('review_store' => $this->getTable('review/review_store')),
                                         'rating_option_vote.review_id=review_store.review_id AND review_store.store_id = :store_id',
                                         array())
                                     ->join(
                                         array('rst' => $this->getTable('rating/rating_store')),
                                         'rst.rating_id = rating_option_vote.rating_id AND rst.store_id = :rst_store_id',
                                         array())
                                     ->join(array('review' => $this->getTable('review/review')),
                                            'review_store.review_id=review.review_id AND review.status_id=1',
                                            array())
                                     ->where($inCond)
                                     ->where('rating_option_vote.entity_pk_value IN(?)', $helper->getProductIdsSelect())
                                     ->group('rating_option_vote.rating_id');
                $bind      = array(
                    ':store_id'     => (int)$storeId,
                    ':rst_store_id' => (int)$storeId,
                );
                $data      = $this->getConnection()->fetchAll($select, $bind);

                foreach($data as $item) {
                    $rating = $this->getItemById($item['rating_id']);
                    if($rating && $item['count'] > 0) {
                        $rating->setSummary($item['sum'] / $item['count']);
                    }
                }
                return $this;
            }
        }
        return parent::addEntitySummaryToItem($entityPkValue, $storeId);
    }
}