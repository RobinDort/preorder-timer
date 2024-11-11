<?php
$GLOBALS['TL_DCA']['tl_preorder_settings'] = [
    'config' => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql' => [
            'keys' => ['id' => 'primary'],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode'                  => 1,
            'fields'                => ['preorder_closed_shop_date', 'preorder_closed_shop_time_status'],
            'panelLayout'           => 'search,limit',
        ],
        'label' => [
            'fields'                => ['preorder_closed_shop_date', 'preorder_closed_shop_time_status'],
            'format'                => '%s',
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_preorder_settings']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.svg',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_preorder_settings']['delete'],
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
        'preorder_closed_shop_date' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_preorder_settings']['preorder_closed_shop_date'],
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(19) NOT NULL default ''",
        ],
        'preorder_closed_shop_time_status' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_preorder_settings']['preorder_closed_shop_time_status'],
            'inputType' => 'select',
            'options'   => ['1', '2', '3'],
            'reference' => [
                '1' => &$GLOBALS['TL_LANG']['tl_preorder_settings']['preorder_closed_shop_time_status_option.1'],
                '2' => &$GLOBALS['TL_LANG']['tl_preorder_settings']['preorder_closed_shop_time_status_option.2'],
                '3' => &$GLOBALS['TL_LANG']['tl_preorder_settings']['preorder_closed_shop_time_status_option.3']
            ],
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
    ],
];
?>