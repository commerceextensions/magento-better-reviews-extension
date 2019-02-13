<?php

class CommerceExtensions_BetterReviews_Block_Review_Helper extends Mage_Review_Block_Helper
{
    /**
     * @param $product
     * @param $templateType
     * @param $displayIfNoReviews
     *
     * @return string
     */
    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        if($this->getBetterReviewsEnabled($product)) {
            $this->_availableTemplates['default'] = 'commerceextensions/betterreviews/review/helper/summary.phtml';
            $this->_availableTemplates['short']   = 'commerceextensions/betterreviews/review/helper/summary_short.phtml';
        }
        return parent::getSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    /**
     * @return bool
     */
    public function getShowStructuredData()
    {
        $product = $this->getProduct();
        if(!$product instanceof Mage_Catalog_Model_Product){
            return false;
        }
        return $this->getStructuredDataEnabled($product) && $this->getRatingSummary() && $this->isCurrentProduct($product) && $this->helper('betterreviews')->isProductPage();
    }

    /**
     * @return string
     */
    public function getReviewsUrl()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->getProduct();

        if($this->getBetterReviewsEnabled($product)){
            if(($this->helper('betterreviews')->isProductPageReviews() || ($this->helper('betterreviews')->isReviewListReviews() && !$this->getShowReviewsOnProductPage($product))) && $this->isCurrentProduct($product)){
                return '#customer-reviews';
            } elseif($this->getShowReviewsOnProductPage($product)) {
                return $product->getProductUrl() . '#customer-reviews';
            } else {
                return parent::getReviewsUrl() . '#customer-reviews';
            }
        }
        return parent::getReviewsUrl();
    }

    /**
     * @return string
     */
    public function getReviewsFormUrl()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->getProduct();

        if($this->getBetterReviewsEnabled($product)){
            if(($this->helper('betterreviews')->isProductPageReviews() || ($this->helper('betterreviews')->isReviewListReviews() && !$this->getShowReviewsOnProductPage($product))) && $this->isCurrentProduct($product)){
                return '#review-form';
            } elseif($this->getShowReviewsOnProductPage($product)) {
                return $product->getProductUrl() . '#review-form';
            } else {
                return parent::getReviewsUrl() . '#review-form';
            }
        }
        return parent::getReviewsUrl();
    }

    /**
     * @param \Mage_Catalog_Model_Product $product
     *
     * @return \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract
     */
    public function getBetterReviewsTypeInstance(Mage_Catalog_Model_Product $product)
    {
        return Mage::getSingleton('betterreviews/config')->getTypeInstance($product);
    }

    /**
     * @param \Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function getBetterReviewsEnabled(Mage_Catalog_Model_Product $product)
    {
        return $this->getBetterReviewsTypeInstance($product)->getIsEnabled();
    }

    /**
     * @param \Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function getShowReviewsOnProductPage(Mage_Catalog_Model_Product $product)
    {
        if($this->getBetterReviewsEnabled($product)){
            return $this->getBetterReviewsTypeInstance($product)->getShowReviewsOnProductPage();
        }
        return false;
    }

    /**
     * @param \Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function getStructuredDataEnabled(Mage_Catalog_Model_Product $product)
    {
        if($this->getBetterReviewsEnabled($product)){
            return $this->getBetterReviewsTypeInstance($product)->getStructuredDataEnabled();
        }
        return false;
    }

    /**
     * @param \Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function isCurrentProduct(Mage_Catalog_Model_Product $product)
    {
        $currentProduct = Mage::registry('current_product');
        if($currentProduct instanceof Mage_Catalog_Model_Product){
            return $product->getId() == $currentProduct->getId();
        }
        return false;
    }
}