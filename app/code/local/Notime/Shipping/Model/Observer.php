<?php

class Notime_Shipping_Model_Observer
{

    /**
     * Add order id into armystar_ups and send request to UPS
     *
     */
    public function notime_shipping_sales_order_complete(Varien_Event_Observer $observer) {

        $_order = $observer->getEvent()->getOrder();

        if(Mage_Sales_Model_Order::STATE_COMPLETE == $_order->getStatus()) {

            // check if order use Notime shipping
            if('notime_notime' == $_order->getShippingMethod()) {

                $resource = Mage::getSingleton('core/resource');
                $readConnection = $resource->getConnection('core_read');
                $writeConnection = $resource->getConnection('core_write');

                $query = 'SELECT shipment_id FROM notime_shipping WHERE `status` = 0 AND quote_id = '. $_order->getQuoteId() .' LIMIT 1';
                $shipment_id = $readConnection->fetchOne($query);
                if($shipment_id) {
                    try {

                        // send POST request to Notime
                        $shipment_id = $shipment_id;
                        if($shipment_id) {

                            // get customer shipping address
                            $_shippingAddress = $_order->getShippingAddress();
                            if($_shippingAddress->getId()) {
                                $params = array(
                                    'ShipmentId' => $shipment_id,
                                    'Dropoff' => array(
                                        'Name' => $_shippingAddress->getFirstname() .' '. $_shippingAddress->getLastname(),
                                        'ContactName' => $_shippingAddress->getFirstname() .' '. $_shippingAddress->getLastname(),
                                        'Phone' => $_shippingAddress->getTelephone(),
                                        'City' => $_shippingAddress->getCity(),
                                        'CountryCode' => $_shippingAddress->getCountryId(),
                                        'Postcode' => $_shippingAddress->getPostcode(),
                                        'Streetaddress' => implode(' ',$_shippingAddress->getStreet()),
                                        'ContactEmailAddress' => $_shippingAddress->getEmail()
                                    ),
                                    'EndUser' => array(
                                        'FullName' => $_shippingAddress->getFirstname() .' '. $_shippingAddress->getLastname(),
                                        'Phone' => $_shippingAddress->getTelephone(),
                                        'Email' => $_shippingAddress->getEmail()
                                    )
                                );

                                $client = new Varien_Http_Client();

                                $client->setUri('https://v1.notimeapi.com/api/shipment/approve')
                                    ->setConfig(array('timeout' => 30, 'keepalive' => 1))
                                    ->setHeaders(array(
                                    'Ocp-Apim-Subscription-Key' => '493dc25bf9674ccb9c5920a035c1f777',
                                ))
                                    ->setRawData(json_encode($params), 'application/json')
                                    ->setMethod(Zend_Http_Client::POST);

                                $client->setHeaders(array('Content-Type: application/json'));

                                $response = $client->request();

                                if($response->isSuccessful()){
                                    // update status
                                    $writeConnection->update(
                                        'notime_shipping',
                                        array('status' => 1),
                                        'quote_id='.$_order->getQuoteId()
                                    );
                                    $_order->addStatusHistoryComment('Notime->Success: Shipment was approved successfully!')->save();
                                } else {
                                    Mage::log('ERROR:'.$response->getMessage(),false,'notime-shipping.log');
                                }
                            }
                        }
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(),false,'notime-shipping.log');
                    }
                }
            }
        }
    }

    /**
     * save notime shipmentId to table 'notime_shipping'
     *
     */
    public function notime_shipping_save_shipment_id($event) {

        if(!Mage::getStoreConfig('carriers/notime/active', Mage::app()->getStore()->getId())) return;

        $_quote  = $event->getQuote();

        $request = Mage::app()->getRequest();
        $shipmentId = trim($request->getPost('notime_shipment_id'));

        if($shipmentId == '') { return; }

        try {

            $resource = Mage::getSingleton('core/resource');
            $writeConnection = $resource->getConnection('core_write');

            $writeConnection->delete(
                "notime_shipping",
                "quote_id=".$_quote->getId()
            );
            $writeConnection->insert(
                "notime_shipping",
                array("quote_id" => $_quote->getId(),"shipment_id" => $shipmentId, "status" => 0)
            );

        } catch (Exception $e) {
            mage::log('Error when processing shipping method:'.$e->getMessage(), false, 'notime_shipping.log');
        }
    }

    /**
     * @param $observer
     */
    public function notime_shipping_core_block_html_after($observer)
    {
        if(!Mage::getStoreConfig('carriers/notime/active', Mage::app()->getStore()->getId())) return;
        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        $template = $block->getTemplate();

        if(strpos($template, 'shipping_method/available.phtml') || $template=='onestepcheckout/shipping_method.phtml' || $template == 'onestepcheckout/onestepcheckout/shipping_method.phtml' || $template == 'onestepcheckout/onestep/shipping.phtml'){
            if(method_exists($block, 'getShippingRates')){
                foreach ($block->getShippingRates() as $code => $_rates){
                    if($code == 'notime'){

                        $endingToken = '</label>';
                        $html = $observer->getTransport()->getHtml();
                        if($pos1 = strpos($html, 'value="notime_notime"')){
                            $pos1 += strlen($endingToken);
                            if($pos2 = strpos($html, $endingToken, $pos1)){

                                $carrier = Mage::getModel('shipping/config')->getCarrierInstance($code);
                                $injectBlock = $block->getLayout()->createBlock($carrier->getFormBlock());

                                $htmlBefore = substr($html,0,$pos2+strlen($endingToken));
                                $htmlAfter = substr($html,$pos2);
                                $observer->getTransport()->setHtml($htmlBefore.$injectBlock->toHtml().$htmlAfter);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Apply Notime shipping fee
     * @param Varien_Event_Observer $observer
     * @return Notime_Shipping_Model_Observer
     */
    public function notime_shipping_salesQuoteCollectTotalsBefore(Varien_Event_Observer $observer){

        $shipmentFee = Mage::getSingleton('core/session')->getNotimeShipmentFee();
        if($shipmentFee == -1) {
            return $this;
        }

        $quote = $observer->getQuote();
        $address = $quote->getShippingAddress();

        if('shipping' == $address->getAddressType()) {
            if('notime_notime' == $address->getShippingMethod()) {

                $store = Mage::app()->getStore($quote->getStoreId());

                $carriers = Mage::getStoreConfig('carriers', $store);
                foreach ($carriers as $carrierCode => $carrierConfig) {
                    if($carrierCode == 'notime') {
                        $store->setConfig("carriers/notime/price", $shipmentFee);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function notime_shipping_salesOrderPlaceAfter(Varien_Event_Observer $observer) {

        $_order = $observer->getOrder();

        if('notime_notime' == $_order->getShippingMethod()) {
            $_order
                ->setShippingDescription($_order->getShippingDescription().' ('.Mage::getSingleton('core/session')->getNotimeShippingInfo().')')
                ->addStatusHistoryComment('Notime->ShipmentId: '.Mage::getSingleton('core/session')->getNotimeShipmentId())
                ->save();
        }

        Mage::getSingleton('core/session')->unsNotimeShipmentFee();
        Mage::getSingleton('core/session')->unsNotimeShippingInfo();
        Mage::getSingleton('core/session')->unsNotimeShipmentId();
        Mage::getSingleton('core/session')->unsNotimeShipmentPostcode();
        Mage::getSingleton('core/session')->unsNotimeSelectedTimewindowdate();
        Mage::getSingleton('core/session')->unsNotimeSelectedServiceguid();

    }
}