<?php

class Notime_Shipping_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            $shipmentFee = $this->getRequest()->getPost('shipping_fee', '');
            if($shipmentFee) {

                Mage::getSingleton('core/session')->setNotimeShipmentFee($shipmentFee);

                $_quote = Mage::getModel('checkout/cart')->getQuote();
                $address = $_quote->getShippingAddress();

                $address->setShippingAmount($shipmentFee);
                $address->setBaseShippingAmount($shipmentFee);

                $address->setCollectShippingRates(true)->save();

                $_quote->collectTotals()->save();
            }

            return true;
        }
    }
}
