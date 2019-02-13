<?php

abstract class CommerceExtensions_BetterReviews_Model_Config_Product_Abstract extends CommerceExtensions_BetterReviews_Model_Config
{
    /**
     * @var null|string
     */
    protected $_xmlPathEnabled;

    /**
     * @var string
     */
    protected $_xmlPathIncludeAssociatedReviews;

    /**
     * @var null|string
     */
    protected $_xmlPathReviewsOnProductPage;

    /**
     * @var string
     */
    protected $_xmlPathChangeBlockTitle;

    /**
     * @var string
     */
    protected $_xmlPathBlockTitlePattern;

    /**
     * @var string
     */
    protected $_xmlPathReviewsListOrder;

    /**
     * @var string
     */
    protected $_xmlPathReviewsListDir;

    /**
     * @var string
     */
    protected $_xmlPathStructuredDataEnabled;

    /**
     * @var string
     */
    protected $_xmlPathShowProductName;

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        if(!$this->hasData('product_type_enabled')){
            if(parent::getIsEnabled()){
                $this->setData('product_type_enabled',(bool)Mage::getStoreConfig($this->_xmlPathEnabled, $this->getStoreId()));
            } else {
                $this->setData('product_type_enabled',false);
            }
        }
        return $this->getData('product_type_enabled');
    }

    /**
     * @return bool
     */
    public function getShowReviewsOnProductPage()
    {
        if(!$this->hasData('show_reviews_on_product_page')){
            $this->setData('show_reviews_on_product_page',(bool)Mage::getStoreConfig($this->_xmlPathReviewsOnProductPage, $this->getStoreId()));
        }
        return $this->getData('show_reviews_on_product_page');
    }

    /**
     * @return bool
     */
    public function getCanIncludeAssociatedReviews()
    {
        if($path = $this->_xmlPathIncludeAssociatedReviews){
            if(!$this->hasData('include_associated_product_reviews')){
                $this->setData('include_associated_product_reviews',(bool)Mage::getStoreConfig($path, $this->getStoreId()));
            }
            return $this->getData('include_associated_product_reviews');
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getChangeBlockTitle()
    {
        if(!$this->hasData('change_block_title')){
            $this->setData('change_block_title',(bool)Mage::getStoreConfig($this->_xmlPathChangeBlockTitle, $this->getStoreId()));
        }
        return $this->getData('change_block_title');
    }

    /**
     * @return bool
     */
    public function getBlockTitlePattern()
    {
        if(!$this->hasData('block_title_pattern')){
            $this->setData('block_title_pattern',Mage::getStoreConfig($this->_xmlPathBlockTitlePattern, $this->getStoreId()));
        }
        return $this->getData('block_title_pattern');
    }

    /**
     * @param \Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public function getReviewsBlockTitle(Mage_Catalog_Model_Product $product)
    {
        $replacements = array(
            '{{name}}' => $product->getData('name'),
            '{{sku}}'  => $product->getData('sku')
        );
        return str_ireplace(array_keys($replacements), array_values($replacements), $this->getBlockTitlePattern());
    }

    /**
     * @return string
     */
    public function getReviewsListOrder()
    {
        if(!$this->hasData('reviews_list_order')){
            $this->setData('reviews_list_order',Mage::getStoreConfig($this->_xmlPathReviewsListOrder, $this->getStoreId()));
        }
        return $this->getData('reviews_list_order');
    }

    /**
     * @return string
     */
    public function getReviewsListDir()
    {
        if(!$this->hasData('reviews_list_dir')){
            $this->setData('reviews_list_dir',Mage::getStoreConfig($this->_xmlPathReviewsListDir, $this->getStoreId()));
        }
        return $this->getData('reviews_list_dir');
    }

    /**
     * @return bool
     */
    public function getStructuredDataEnabled()
    {
        if(!$this->hasData('structured_data_enabled')){
            $this->setData('structured_data_enabled',(bool)Mage::getStoreConfig($this->_xmlPathStructuredDataEnabled, $this->getStoreId()));
        }
        return $this->getData('structured_data_enabled');
    }

    /**
     * @return bool
     */
    public function getShowProductName()
    {
        if(!$this->getCanIncludeAssociatedReviews()){
            $this->setData('show_product_name',false);
        }

        if($path = $this->_xmlPathShowProductName){
            if(!$this->hasData('show_product_name')){
                $this->setData('show_product_name',(bool)Mage::getStoreConfig($path, $this->getStoreId()));
            }
            return $this->getData('show_product_name');
        }
        return false;
    }

}