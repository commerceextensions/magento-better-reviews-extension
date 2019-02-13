<?php

class CommerceExtensions_BetterReviews_Model_Helper extends Mage_Core_Model_Abstract
{
    /**
     * @var \Magento_Db_Adapter_Pdo_Mysql
     */
    protected $_readAdapter;

    /**
     * @var null|int
     */
    protected $_storeId;

    /**
     * @var int
     */
    protected $_tableSuffixNum = 0;

    /**
     * @var array
     */
    protected $_productIdsFilter = array();

    /**
     * @var array
     */
    protected $_productStatusFilter = array(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

    /**
     * @var array
     */
    protected $_includeAssociatedProductTypes = array();

    /**
     * @var array
     */
    protected $_reviewStatusFilter = array(Mage_Review_Model_Review::STATUS_APPROVED);

    protected function _construct()
    {
        $this->_readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_storeId     = Mage::app()->getStore()->getId();
    }

    /**
     * @return \Magento_Db_Adapter_Pdo_Mysql
     */
    protected function _getReadAdapter()
    {
        return $this->_readAdapter;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Mage_Catalog_Model_Product::DEFAULT_STORE_ID;
    }

    /**
     * @param int|array|\Mage_Catalog_Model_Product $product
     *
     * @return $this
     */
    public function setProductIdsFilter($product)
    {
        $this->_productIdsFilter = $this->_prepareProductIdsForQuery($product);
        return $this;
    }

    /**
     * @return array
     */
    public function getProductIdsFilter()
    {
        return $this->_productIdsFilter;
    }

    /**
     * @param int|array $status
     *
     * @return $this
     */
    public function setProductStatusFilter($status)
    {
        $this->_productStatusFilter = is_array($status) ? $status : array($status);
        return $this;
    }

    /**
     * @return array
     */
    public function getProductStatusFilter()
    {
        return $this->_productStatusFilter;
    }

    /**
     * @param int|array $status
     *
     * @return $this
     */
    public function setReviewStatusFilter($status)
    {
        $this->_reviewStatusFilter = is_array($status) ? $status : array($status);
        return $this;
    }

    /**
     * @return array
     */
    public function getReviewStatusFilter()
    {
        return $this->_reviewStatusFilter;
    }

    /**
     * @param string $typeId - valid values are grouped, configurable, and bundle
     *
     * @return $this
     */
    public function includeAssociatedProducts($typeId)
    {
        if(!in_array($typeId, $this->_includeAssociatedProductTypes)) {
            $this->_includeAssociatedProductTypes[] = $typeId;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getIncludedAssociatedProductTypes()
    {
        return $this->_includeAssociatedProductTypes;
    }

    /**
     * @return \Varien_Db_Select
     */
    public function getGroupedAssociatedProductsSelect()
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from(array('product_link' => Mage::helper('cecore/db')->getTableName('catalog_product_link')), array('parent_id'  => 'product_id',
                                                                                                                                            'product_id' => 'linked_product_id'));
        $select->where('link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);

        if($ids = $this->getProductIdsFilter()) {
            $select->where('product_link.product_id IN(?)', $ids);
        }

        if($status = $this->getProductStatusFilter()) {
            $select->joinInner(
                array('status_select' => $this->getProductAttributeSelect('status')),
                "product_link.linked_product_id = status_select.entity_id",
                array());
            $select->where('status_select.status IN(?)', $status);
        }
        return $select;
    }

    /**
     * @return \Varien_Db_Select
     */
    public function getConfigurableAssociatedProductsSelect()
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from(array('product_link' => Mage::helper('cecore/db')->getTableName('catalog_product_super_link')), array('parent_id', 'product_id'));

        if($ids = $this->getProductIdsFilter()) {
            $select->where('product_link.parent_id IN(?)', $ids);
        }

        if($status = $this->getProductStatusFilter()) {
            $select->joinInner(
                array('status_select' => $this->getProductAttributeSelect('status')),
                "product_link.product_id = status_select.entity_id",
                array());
            $select->where('status_select.status IN(?)', $status);
        }
        return $select;
    }

    /**
     * @return \Varien_Db_Select
     */
    public function getBundleAssociatedProductsSelect()
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from(array('product_link' => Mage::helper('cecore/db')->getTableName('catalog_product_bundle_selection')), array('parent_id' => 'parent_product_id',
                                                                                                                                                        'product_id'));

        if($ids = $this->getProductIdsFilter()) {
            $select->where('product_link.parent_product_id IN(?)', $ids);
        }

        if($status = $this->getProductStatusFilter()) {
            $select->joinInner(
                array('status_select' => $this->getProductAttributeSelect('status')),
                "product_link.product_id = status_select.entity_id",
                array());
            $select->where('status_select.status IN(?)', $status);
        }
        return $select;
    }

    /**
     * @param null|int|array|Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    protected function _prepareProductIdsForQuery($product = null)
    {
        $entityIds = array();
        if($product instanceof Mage_Catalog_Model_Product) {
            $entityIds[] = $product->getId();
        } elseif(is_numeric($product)) {
            $entityIds[] = $product;
        } elseif(is_array($product)) {
            $entityIds = array_merge($entityIds, $product);
        }
        return $entityIds;
    }

    /**
     * @return \Varien_Db_Select
     */
    public function getProductIdPairsSelect()
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from(array('main_table' => Mage::helper('cecore/db')->getTableName('catalog_product_entity')), array('parent_id'  => 'entity_id',
                                                                                                                                            'product_id' => 'entity_id'));

        if($ids = $this->getProductIdsFilter()) {
            $select->where('main_table.entity_id IN(?)', $ids);
        }

        if($status = $this->getProductStatusFilter()) {
            $select->joinInner(
                array('status_select' => $this->getProductAttributeSelect('status')),
                "main_table.entity_id = status_select.entity_id",
                array());
            $select->where('status_select.status IN(?)', $status);
        }

        $selects = array($select);
        $types   = $this->getIncludedAssociatedProductTypes();

        if(in_array(Mage_Catalog_Model_Product_Type::TYPE_GROUPED, $types)) {
            $selects[] = $this->getGroupedAssociatedProductsSelect();
        }

        if(in_array(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, $types)) {
            $selects[] = $this->getConfigurableAssociatedProductsSelect();
        }

        if(in_array(Mage_Catalog_Model_Product_Type::TYPE_BUNDLE, $types)) {
            $selects[] = $this->getBundleAssociatedProductsSelect();
        }

        if(count($selects) > 1) {
            $select = $adapter->select()->union($selects);
            $select->group(new Zend_Db_Expr('parent_id, product_id'));
        }
        return $select;
    }

    /**
     * @return \Varien_Db_Select
     */
    public function getProductIdsSelect()
    {
        return $this->_getReadAdapter()->select()->from($this->getProductIdPairsSelect(), 'product_id');
    }

    /**
     * @return array
     */
    public function getProductIds()
    {
        return $this->_getReadAdapter()->fetchCol($this->getProductIdsSelect());
    }

    /**
     * This is NOT the entity id of a product. This is the entity id of product within the reviews system
     *
     * @return int
     */
    public function getReviewProductEntityType()
    {
        /** @var Magento_Db_Adapter_Pdo_Mysql $adapter */
        $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select  = $adapter->select()->from(Mage::helper('cecore/db')->getTableName('review_entity'), 'entity_id');
        $select->where('entity_code = ?', 'product');
        return $adapter->fetchOne($select);
    }

    /**
     * @return \Varien_Db_Select
     */
    function getReviewsCountSelect()
    {
        $helper  = Mage::helper('cecore/db');
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from(
            array('review' => $helper->getTableName('review')),
            array('entity_pk_value' => 'entity_id_pairs.parent_id',
                  'reviews_count'   => new Zend_Db_Expr('COUNT(*)'))
        );
        $select->where('review.entity_id = ?', $this->getReviewProductEntityType());
        $select->joinInner(
            array('store' => $helper->getTableName('review_store')),
            "review.review_id = store.review_id",
            'store_id');
        $select->where('store.store_id IN(?)', $this->getStoreId());
        if($status = $this->getReviewStatusFilter()) {
            $select->where('review.status_id IN(?)', $status);
        }
        $select->joinInner(
            array('entity_id_pairs' => $this->getProductIdPairsSelect()),
            "review.entity_pk_value = entity_id_pairs.product_id",
            array('*'));
        $select->group('entity_id_pairs.parent_id');
        return $select;
    }

    /**
     * @return \Varien_Db_Select
     */
    function getRatingSummarySelect()
    {
        $helper  = Mage::helper('cecore/db');
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from(
            array('rating_option_vote' => $helper->getTableName('rating_option_vote')),
            array('entity_pk_value' => 'entity_id_pairs.parent_id',
                  'rating_summary'  => new Zend_Db_Expr('AVG(rating_option_vote.percent)'))
        );
        $select->joinInner(
            array('review_store' => $helper->getTableName('review_store')),
            "rating_option_vote.review_id = review_store.review_id",
            array('store_id'));
        $select->where('review_store.store_id IN(?)', $this->getStoreId());

        $select->joinInner(
            array('rating_store' => $helper->getTableName('rating_store')),
            "rating_option_vote.rating_id = rating_store.rating_id",
            array(''));
        $select->where('rating_store.store_id IN(?)', $this->getStoreId());

        if($status = $this->getReviewStatusFilter()) {
            $select->joinInner(
                array('review' => $helper->getTableName('review')),
                "rating_option_vote.review_id = review.review_id",
                array());
            $select->where('review.status_id IN(?)', $status);
        }
        $select->joinInner(
            array('entity_id_pairs' => $this->getProductIdPairsSelect()),
            "rating_option_vote.entity_pk_value = entity_id_pairs.product_id",
            array());
        $select->group('entity_id_pairs.parent_id');
        return $select;
    }

    /**
     * @return \Varien_Db_Select
     */
    public function getFullSummarySelect()
    {
        $adapter = $this->_getReadAdapter();

        $reviewCountSelect   = $this->getReviewsCountSelect();
        $ratingSummarySelect = $this->getRatingSummarySelect();
        $select              = $adapter->select()->from(array('review_count_select' => $reviewCountSelect));
        $select->joinLeft(array('rating_summary_select' => $ratingSummarySelect), "review_count_select.entity_pk_value = rating_summary_select.entity_pk_value", array('rating_summary'));
        return $select;
    }

    /**
     * @return array
     */
    public function getFullSummary()
    {
        $summary = array();
        $result  = $this->_getReadAdapter()->fetchAll($this->getFullSummarySelect());
        foreach($result as &$item) {
            $summary[$item['entity_pk_value']] = $item;
        }
        return $summary;
    }

    /**
     * @param $attributeCode
     *
     * @return \Varien_Db_Select
     */
    public function getProductAttributeSelect($attributeCode)
    {
        $adapter        = $this->_getReadAdapter();
        $resource       = Mage::getResourceModel('catalog/product');
        $attribute      = $resource->getAttribute($attributeCode);
        $table          = $attribute->getBackendTable();
        $defaultAlias   = "default_table_{$attributeCode}_{$this->_tableSuffixNum}";
        $storeAlias     = "store_table_{$attributeCode}_{$this->_tableSuffixNum}";
        $entityTypeId   = $attribute->getEntityTypeId();
        $attributeId    = $attribute->getId();
        $storeId        = $this->getStoreId();
        $defaultStoreId = $this->getDefaultStoreId();

        if($storeId == $defaultStoreId) {
            $select = $adapter->select()->from(array($storeAlias => $table), array("{$storeAlias}.entity_id", $attributeCode => new Zend_Db_Expr("{$storeAlias}.value")));
            $select->where("{$storeAlias}.entity_type_id = ?", $entityTypeId);
            $select->where("{$storeAlias}.attribute_id = ?", $attributeId);
            $select->where("{$storeAlias}.store_id = ?", $storeId);
        } else {
            $select = $adapter->select()->from(array($defaultAlias => $table), array("{$defaultAlias}.entity_id",
                                                                                     $attributeCode => new Zend_Db_Expr("COALESCE({$storeAlias}.value,{$defaultAlias}.value)")));
            $select->where("{$defaultAlias}.entity_type_id = ?", $entityTypeId);
            $select->where("{$defaultAlias}.attribute_id = ?", $attributeId);
            $select->where("{$defaultAlias}.store_id = ?", $defaultStoreId);
            $select->joinLeft(array($storeAlias => $table),
                              "{$defaultAlias}.entity_id = {$storeAlias}.entity_id AND
                {$storeAlias}.entity_type_id = {$entityTypeId} AND                
                {$storeAlias}.attribute_id = {$attributeId} AND
                {$storeAlias}.store_id = {$storeId}"
                , array());
        }
        $this->_tableSuffixNum++;
        return $select;
    }
}