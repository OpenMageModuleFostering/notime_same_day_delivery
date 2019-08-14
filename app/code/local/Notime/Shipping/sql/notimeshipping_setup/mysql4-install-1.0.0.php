<?php

/* @var $this Mage_Eav_Model_Entity_Setup */
$this->startSetup();

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'notime_widget', array(
    'group'                => 'General',
    'input'                => 'select',
    'type'                 => 'int',
    'source'               => 'eav/entity_attribute_source_boolean',
    'backend'              => '',
    'frontend'             => '',
    'label'                => Mage::helper('notimeshipping')->__('Show Notime widget ?'),
    'global'               => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'required'             => false,
    'default'              => 1,
    'default_value'        => 1,
    'searchable'           => false,
    'filterable'           => false,
    'user_defined'         => true,
    'visible'              => false,
    'visible_on_front'     => false,
    'unique'               => false,
    'is_configurable'      => false,
    'used_for_promo_rules' => false
));

$this->endSetup();