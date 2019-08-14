<?php
class Notime_Shipping_Block_Product_Widget extends Mage_Core_Block_Template
{
    public function __construct() {
        $this->setTemplate('notime/product/widget.phtml');
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        $product = $this->_getData('product');
        if (!$product) {
            $product = Mage::registry('product');
        }
        return $product;
    }

    /**
     * @return mixed|string
     * Get Notime widget
     */
    public function getNotimeWidget() {
        if(Mage::getStoreConfig('carriers/notime/active', Mage::app()->getStore()->getId())
            && Mage::getStoreConfig('carriers/notime/show_product_widget', Mage::app()->getStore()->getId())
                && $this->getProduct()->getNotimeWidget()
            ) {
            return
                Mage::getStoreConfig('carriers/notime/widget_viewmode_button', Mage::app()->getStore()->getId())
                .Mage::getStoreConfig('carriers/notime/widget_viewmode_code', Mage::app()->getStore()->getId());
        }
        return '';
    }
}