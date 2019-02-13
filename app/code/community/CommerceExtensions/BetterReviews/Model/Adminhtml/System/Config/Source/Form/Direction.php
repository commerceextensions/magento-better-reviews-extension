<?php

class CommerceExtensions_BetterReviews_Model_Adminhtml_System_Config_Source_Form_Direction extends Mage_Core_Model_Abstract
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Varien_Db_Select::SQL_ASC, 'label' => Mage::helper('betterreviews')->__('Ascending')),
            array('value' => Varien_Db_Select::SQL_DESC, 'label' => Mage::helper('betterreviews')->__('Descending'))
        );
    }
}