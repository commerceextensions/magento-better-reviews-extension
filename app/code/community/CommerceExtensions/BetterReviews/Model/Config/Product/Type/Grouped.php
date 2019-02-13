<?php

class CommerceExtensions_BetterReviews_Model_Config_Product_Type_Grouped extends CommerceExtensions_BetterReviews_Model_Config_Product_Abstract
{
    const XML_PATH_ENABLED                    = 'betterreviews/grouped_product/enabled';
    const XML_PATH_INCLUDE_ASSOCIATED_REVIEWS = 'betterreviews/grouped_product/include_associated_product_reviews';
    const XML_PATH_REVIEWS_ON_PRODUCT_PAGE    = 'betterreviews/grouped_product/reviews_on_product_page';
    const XML_PATH_CHANGE_BLOCK_TITLE         = 'betterreviews/grouped_product/change_block_title';
    const XML_PATH_BLOCK_TITLE_PATTERN        = 'betterreviews/grouped_product/block_title_pattern';
    const XML_PATH_REVIEWS_LIST_ORDER         = 'betterreviews/grouped_product/list_order';
    const XML_PATH_REVIEWS_LIST_DIR           = 'betterreviews/grouped_product/list_dir';
    const XML_PATH_STRUCTURED_DATA_ENABLED    = 'betterreviews/grouped_product/structured_data_enabled';
    const XML_PATH_SHOW_PRODUCT_NAME          = 'betterreviews/grouped_product/show_product_name';

    /**
     * @var string
     */
    protected $_xmlPathEnabled = self::XML_PATH_ENABLED;

    /**
     * @var string
     */
    protected $_xmlPathIncludeAssociatedReviews = self::XML_PATH_INCLUDE_ASSOCIATED_REVIEWS;

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

    /**
     * @var string
     */
    protected $_xmlPathShowProductName = self::XML_PATH_SHOW_PRODUCT_NAME;
}