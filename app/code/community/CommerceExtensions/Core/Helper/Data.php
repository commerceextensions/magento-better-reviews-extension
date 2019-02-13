<?php

class CommerceExtensions_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return bool
     */
    public function isAdminArea()
    {
        return Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'admin';
    }

    /**
     * @return array
     */
    public function getHandles()
    {
        return Mage::app()->getLayout()->getUpdate()->getHandles();
    }

    /**
     * @return string
     */
    public function getFullActionName()
    {
        return Mage::app()->getFrontController()->getAction()->getFullActionName();
    }

    /**
     * @param string $filepath
     *
     * @return string
     */
    public function getFileExtension($filepath)
    {
        $filename  = basename($filepath);
        $parts     = explode('.', $filename);
        $extension = end($parts);
        return $extension;
    }
}