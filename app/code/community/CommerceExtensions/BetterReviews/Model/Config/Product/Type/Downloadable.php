<?php

class CommerceExtensions_BetterReviews_Model_Config_Product_Type_Downloadable extends CommerceExtensions_BetterReviews_Model_Config_Product_Abstract
{
    const XML_PATH_ENABLED                 = 'betterreviews/downloadable_product/enabled';
    const XML_PATH_REVIEWS_ON_PRODUCT_PAGE = 'betterreviews/downloadable_product/reviews_on_product_page';
    const XML_PATH_CHANGE_BLOCK_TITLE      = 'betterreviews/downloadable_product/change_block_title';
    const XML_PATH_BLOCK_TITLE_PATTERN     = 'betterreviews/downloadable_product/block_title_pattern';
    const XML_PATH_REVIEWS_LIST_ORDER      = 'betterreviews/downloadable_product/list_order';
    const XML_PATH_REVIEWS_LIST_DIR        = 'betterreviews/downloadable_product/list_dir';
    const XML_PATH_STRUCTURED_DATA_ENABLED = 'betterreviews/downloadable_product/structured_data_enabled';

    /**
     * @var string
     */
    protected $_xmlPathEnabled = self::XML_PATH_ENABLED;

    /**
     * @var string
     */
    protected $_xmlPathReviewsOnProductPage = self::XML_PATH_REVIEWS_ON_PRODUCT_PAGE;

    /**
     * @var string
     */
    protected $_xmlPathChangeBlockTitle = self::XML_PATH_CHANGE_BLOCK_TITLE;

    /**
     * @var string
     */
    protected $_xmlPathBlockTitlePattern = self::XML_PATH_BLOCK_TITLE_PATTERN;

    /**
     * @var string
     */
    protected $_xmlPathReviewsListOrder = self::XML_PATH_REVIEWS_LIST_ORDER;

    /**
     * @var string
     */
    protected $_xmlPathReviewsListDir = self::XML_PATH_REVIEWS_LIST_DIR;

    /**
     * @var string
     */
    protected $_xmlPathStructuredDataEnabled = self::XML_PATH_STRUCTURED_DATA_ENABLED;
}