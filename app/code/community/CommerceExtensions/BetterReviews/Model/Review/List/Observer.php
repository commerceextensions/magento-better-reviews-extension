<?php

class CommerceExtensions_BetterReviews_Model_Review_List_Observer
{
    /**
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function redirectToProductPage(Varien_Event_Observer $observer)
    {
        $productId = $observer->getEvent()->getControllerAction()->getRequest()->getParam('id');

        /** @var \CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
        $typeInstance = Mage::getSingleton('betterreviews/config')->getTypeInstance($productId);
        if($typeInstance->getIsEnabled()) {
            if($typeInstance->getShowReviewsOnProductPage()) {
                $product = Mage::registry('current_product');
                if(!$product instanceof Mage_Catalog_Model_Product) {
                    $product = Mage::getModel('catalog/product')->load($productId);
                }

                Mage::app()
                    ->getResponse()
                    ->setRedirect($product->getProductUrl() . '#customer-reviews', 301)
                    ->sendResponse();
                exit;
            }
        }
        return $this;
    }

    /**
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function setTemplateAndData(Varien_Event_Observer $observer)
    {
        /** @var CommerceExtensions_BetterReviews_Model_Config $config */
        $config = Mage::getSingleton('betterreviews/config');
        if($config->getIsEnabled()) {
            /** @var Mage_Core_Model_Layout $layout */
            $layout = Mage::app()->getLayout();
            foreach($layout->getAllBlocks() as $block) {
                if($block instanceof Mage_Review_Block_Product_View_List) {

                    /** @var Mage_Catalog_Model_Product $product */
                    $product = $block->getProduct();

                    /** @var CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
                    $typeInstance = $config->getTypeInstance($product);

                    if($typeInstance->getIsEnabled()) {

                        /** @var Mage_Review_Model_Resource_Review_Collection $collection */
                        $collection = $block->getReviewsCollection();

                        if($typeInstance->getChangeBlockTitle()) {
                            $title = $typeInstance->getReviewsBlockTitle($product);
                            $block->setBlockTitle($title);
                        }

                        if($typeInstance->getReviewsListOrder() == 'length') {
                            $collection->getSelect()->reset(Zend_Db_Select::ORDER);
                            $collection->getSelect()->order("LENGTH(detail.detail) {$typeInstance->getReviewsListDir()}");
                        }
//TODO fix the reviews URL and also check visibility of form. Also check form submission
                        $block->setShowStructuredData((bool)$typeInstance->getStructuredDataEnabled());
                        $block->setShowProductName((bool)$typeInstance->getShowProductName());

                        $this->_joinProductNameToCollection($collection);
                        $block->setTemplate('commerceextensions/betterreviews/review/product/view/list.phtml');
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param \Mage_Review_Model_Resource_Review_Collection $collection
     *
     * @return $this
     */
    protected function _joinProductNameToCollection(Mage_Review_Model_Resource_Review_Collection $collection)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->joinAttribute(
            'name',
            "catalog_product/name",
            'entity_id',
            null,
            'left',
            Mage::app()->getStore()->getId()
        );
        $collection->getSelect()->join(
            array('products_select' => $productCollection->getSelect()),
            'main_table.entity_pk_value = products_select.entity_id',
            array("product_name" => 'name'));
        return $this;
    }

    /**
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function updateToolbarText(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if($block instanceof Mage_Page_Block_Html_Pager) {
            if($block->getNameInLayout() == 'product_review_list.toolbar') {
                $transport    = $observer->getTransport();
                $replacements = array(
                    'Item' => 'Review',
                    'item' => 'review'
                );
                $html         = str_replace(array_keys($replacements), $replacements, $transport->getHtml());
                $transport->setHtml($html);
            }
        }
        return $this;
    }

    /**
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function setDetailedRatingTemplate(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if($block instanceof Mage_Rating_Block_Entity_Detailed) {

            /** @var CommerceExtensions_BetterReviews_Model_Config $config */
            $config = Mage::getSingleton('betterreviews/config');
            if($config->getIsEnabled()) {

                /** @var Mage_Catalog_Model_Product $product */
                $product = Mage::registry('current_product');

                /** @var CommerceExtensions_BetterReviews_Model_Config_Product_Abstract $typeInstance */
                $typeInstance = $config->getTypeInstance($product);

                if($typeInstance->getIsEnabled()) {
                    if($enabled = $typeInstance->getStructuredDataEnabled()) {

                        /** @var CommerceExtensions_BetterReviews_Model_Helper $helper */
                        $helper = Mage::getModel('betterreviews/helper');
                        $helper->setProductIdsFilter($product);
                        $helper->includeAssociatedProducts($typeInstance->getProductType($product));

                        $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
                        $select = $adapter->select()->from($helper->getReviewsCountSelect(),'reviews_count');
                        $count = (int)$adapter->fetchOne($select);

                        $block->setShowStructuredData((bool)$enabled);
                        $block->setReviewsCount($count);
                        $block->setProduct($product);
                        $block->setTemplate('commerceextensions/betterreviews/rating/detailed.phtml');
                    }
                }
            }
        }
        return $this;
    }
}