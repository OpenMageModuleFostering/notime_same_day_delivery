<?php
class Notime_Shipping_Block_ShippingMethod extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function __construct(){
        $this->setTemplate('notime/shipping_method/available.phtml');
    }
}