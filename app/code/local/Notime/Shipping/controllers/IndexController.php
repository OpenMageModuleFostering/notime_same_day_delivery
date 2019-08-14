<?php

class Notime_Shipping_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {

        if ($this->getRequest()->isPost()) {

            $shipmentFee = $this->getRequest()->getPost('shipping_fee', 0);
            $shipmentId = $this->getRequest()->getPost('shipping_id', '-');
            $shipmentPostcode = $this->getRequest()->getPost('shipping_postcode', '');
            $shipmentInfo = $this->getRequest()->getPost('shipping_info', '');

            // widget -> selected window data
            $shipmentTimewindowdate = $this->getRequest()->getPost('shipping_timewindowdate', '');
            $shipmentServiceguid = $this->getRequest()->getPost('shipping_serviceguid', '');

            Mage::getSingleton('core/session')->setNotimeShipmentFee($shipmentFee);
            Mage::getSingleton('core/session')->setNotimeShipmentId($shipmentId);
            Mage::getSingleton('core/session')->setNotimeShipmentPostcode($shipmentPostcode);
            Mage::getSingleton('core/session')->setNotimeShippingInfo($shipmentInfo);

            Mage::getSingleton('core/session')->setNotimeSelectedTimewindowdate($shipmentTimewindowdate);
            Mage::getSingleton('core/session')->setNotimeSelectedServiceguid($shipmentServiceguid);

            $address = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
            $address->setCollectShippingRates(true)->save();

            return true;
        }
    }
}
