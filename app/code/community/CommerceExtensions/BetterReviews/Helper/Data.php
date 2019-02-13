<?php

class CommerceExtensions_BetterReviews_Helper_Data extends CommerceExtensions_Core_Helper_Data
{
    /**
     * @return bool
     */
    public function isProductPage()
    {
        return in_array('catalog_product_view',$this->getHandles());
    }

    /**
     * @return bool
     */
    public function isProductPageReviews()
    {
        return in_array('product_page_reviews',$this->getHandles());
    }

    /**
     * @return bool
     */
    public function isReviewListReviews()
    {
        return in_array('review_product_list',$this->getHandles());
    }
}