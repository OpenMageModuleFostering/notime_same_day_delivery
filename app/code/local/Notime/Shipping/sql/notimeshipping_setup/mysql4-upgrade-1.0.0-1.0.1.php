<?php
/** @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');

$installer->startSetup();

try {
    $installer->run("
    DROP TABLE IF EXISTS {$this->getTable('notime_shipping')};
    CREATE TABLE {$this->getTable('notime_shipping')} (
        `id` INT(11) NOT NULL auto_increment,
        `quote_id` int(6) unsigned NOT NULL default '0',
        `status` tinyint(1) NOT NULL default '0',
        `shipment_id` VARCHAR(255) NOT NULL default '',
        PRIMARY KEY  (`id`),
        KEY `quote_id` ( `quote_id` )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");
}catch (Exception $e) {
    Mage::logException($e);
}
$installer->endSetup();
