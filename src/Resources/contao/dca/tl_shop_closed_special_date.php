<?php

$GLOBALS['TL_DCA']['tl_shop_closed_special_date'] = [
    'config' => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql' => [
            'keys' => ['id,shop_closed_date_id' => 'primary',
            'shop_closed_date_id' => 'index',
            'special_closed_date_time_id' => 'index'],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode'        => 1,
            'fields'      => ['date'],
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields'      => ['date'],
            'format'      => '%s',
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_shop_closed_special_date']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.svg',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_shop_closed_special_date']['delete'],
                'href'  => 'act=delete',
                'icon'  => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'Are you sure?\'))return false;Backend.getScrollOffset();"'
            ],
        ],
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'shop_closed_date_id' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_shop_closed_special_date']['shop_closed_date_id'],
            'inputType' => 'select',
            'foreignKey'=> 'tl_shop_closed_date.id',  // This defines the reference to the shop_closed_date table
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "int(10) unsigned NOT NULL",
        ],
        'special_closed_date_time_id' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_shop_closed_special_date']['shop_closed_date_time.id'],
            'inputType' => 'select',
            'foreignKey'=> 'tl_shop_closed_special_date_time.id', 
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "int(10) unsigned NOT NULL",
        ],
        'tstamp' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ],
    ],
];

?>