<?php

class CommerceExtensions_BetterReviews_Model_Adminhtml_System_Config_Source_Form_Order extends Mage_Core_Model_Abstract
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'default', 'label' => Mage::helper('betterreviews')->__('Default')),
            array('value' => 'length', 'label' => Mage::helper('betterreviews')->__('Review Text Length(Good for SEO)'))
        );
    }
}