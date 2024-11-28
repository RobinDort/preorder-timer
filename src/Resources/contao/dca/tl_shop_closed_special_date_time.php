<?php

$GLOBALS['TL_DCA']['shop_closed_special_date_time'] = [
    'config' => [
        'dataContainer'    => 'Table',
        'enableVersioning' => false,
        'sql' => [
            'keys' => ['id, shop_closed_special_date_id' => 'primary',
            'shop_closed_special_date_id' => 'index'],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode'        => 1,
            'fields'      => ['time'],
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields'      => ['time'],
            'format'      => '%s',
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['shop_closed_special_date_time']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.svg',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['shop_closed_special_date_time']['delete'],
                'href'  => 'act=delete',
                'icon'  => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'Are you sure?\'))return false;Backend.getScrollOffset();"'
            ],
        ],
    ],
    'fields' => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL",
        ],
        'shop_closed_special_date_id' => [
            'label'     => &$GLOBALS['TL_LANG']['shop_closed_special_date_time']['shop_closed_special_date_id'],
            'inputType' => 'select',
            'foreignKey'=> 'shop_closed_special_date.id',  // This defines the reference to the shop_closed_special_date table
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "int(10) unsigned NOT NULL",
        ],
        'tstamp' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ],
        'time' => [
            'label'     => &$GLOBALS['TL_LANG']['shop_closed_special_date_time']['time'],
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
    ],
];

?>