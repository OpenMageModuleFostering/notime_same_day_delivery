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
    protected function _construct() {
        $this->setTemplate('notime/checkout/onepage/shipping_method/notime.phtml');

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
    
    public function getAdditionalText(){
		return Mage::getStoreConfig('carriers/notime/additional_info', Mage::app()->getStore()->getId());
    }

    public function getShipmentId() {

        $this->checkShipmentZipcode();

        $shipmentId = Mage::getSingleton('core/session')->getNotimeShipmentId();
        return ($shipmentId && $shipmentId != '-') ? $shipmentId : '-';
    }

    public function getShipmentFee() {

        $shipmentFee = Mage::getSingleton('core/session')->getNotimeShipmentFee();
        return $shipmentFee ? $shipmentFee : 0;
    }

    public function getShipmentInfo() {

        $shipmentInfo = Mage::getSingleton('core/session')->getNotimeShippingInfo();
        return $shipmentInfo ? $shipmentInfo : '';
    }

    public function getShipmentTimewindowdate() {

        $shipmentTimewindowdate = Mage::getSingleton('core/session')->getNotimeSelectedTimewindowdate();
        return $shipmentTimewindowdate ? '\''.$shipmentTimewindowdate.'\'' : 'null';
    }

    public function getShipmentServiceguid() {

        $shipmentServiceguid = Mage::getSingleton('core/session')->getNotimeSelectedServiceguid();
        return $shipmentServiceguid ? '\''.$shipmentServiceguid.'\'' : 'null';
    }

    /*
     * get shipment zipcode
     */
    public function checkShipmentZipcode() {

        $shippingAddress = $this->getQuote()->getShippingAddress();
        
        $zip = $shippingAddress->getPostcode();
        $zipNotime = Mage::getSingleton('core/session')->getNotimeShipmentPostcode();

        if($zip != $zipNotime) {

            Mage::getSingleton('core/session')->setNotimeShipmentFee(0);
            Mage::getSingleton('core/session')->setNotimeShipmentId('-');
            Mage::getSingleton('core/session')->setNotimeShipmentPostcode('');
            Mage::getSingleton('core/session')->setNotimeShippingInfo('');
            Mage::getSingleton('core/session')->setNotimeSelectedTimewindowdate('');
            Mage::getSingleton('core/session')->setNotimeSelectedServiceguid('');

            $shippingAddress->setCollectShippingRates(true)->save();

        }
    }
}
