<?php

$GLOBALS['TL_DCA']['tl_shop_closed_status'] = [
    'config' => [
        'dataContainer'    => 'Table',
        'enableVersioning' => false,
        'sql' => [
            'keys' => ['id' => 'primary'],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode'        => 1,
            'fields'      => ['status'],
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields'      => ['status'],
            'format'      => '%s',
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_shop_closed_status']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.svg',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_shop_closed_status']['delete'],
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
        'status' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_shop_closed_status']['status'],
            'inputType' => 'select',
            'options'   => ['1', '2', '3'],
            'reference' => [
                '1' => &$GLOBALS['TL_LANG']['tl_shop_closed_status']['shop_closed_status']['option']['1'],
                '2' => &$GLOBALS['TL_LANG']['tl_shop_closed_status']['shop_closed_status']['option']['2'],
                '3' => &$GLOBALS['TL_LANG']['tl_shop_closed_status']['shop_closed_status']['option']['3'],
            ],
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
    ],
];

?>