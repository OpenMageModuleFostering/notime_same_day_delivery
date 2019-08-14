<?php

/**
 * One page checkout status
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Notime_Shipping_Block_Checkout_Onepage_Shipping_Notime extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->setTemplate('notime/checkout/onepage/shipping_method/notime.phtml');

        /*
        $this->getCheckout()->setStepData('shipping_method', array(
            'label'     => Mage::helper('checkout')->__('Shipping Method'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
        */
    }

    public function getFormCode(){
        return 'notime_additional_block';
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }

    /**
     * Return shipping widget code
     * @return mixed|string
     */
    public function getWidgetButton() {
        if(Mage::getStoreConfig('carriers/notime/active', Mage::app()->getStore()->getId())) {
            return Mage::getStoreConfig('carriers/notime/widget_editmode_button', Mage::app()->getStore()->getId());
        }
        return '';
    }

    /**
     * @return string
     */
    public function getWidgetCodeSrc() {
        if(Mage::getStoreConfig('carriers/notime/active', Mage::app()->getStore()->getId())) {
            $matches = array();
            preg_match('/src=[\"\']([^"]*)[\"\']/i', Mage::getStoreConfig('carriers/notime/widget_editmode_code', Mage::app()->getStore()->getId()), $matches) ;
            if(isset($matches[1])) {
                return trim($matches[1]);
            }
        }
        return '';
    }

    /**
     * @return string
     */
    public function getWidgetZipCode() {
        if(Mage::getStoreConfig('carriers/notime/active', Mage::app()->getStore()->getId())) {
            $matches = array();
            preg_match('/ecommerceZipCodeId=(.*?)[\&\'\"]/', Mage::getStoreConfig('carriers/notime/widget_editmode_code', Mage::app()->getStore()->getId()), $matches) ;
            if(isset($matches[1])) {
                return trim($matches[1]);
            }
        }
        return '';
    }

}
