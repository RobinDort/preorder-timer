<?php

$GLOBALS['TL_DCA']['tl_shop_closed_date'] = [
    'config' => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql' => [
            'keys' => ['id' => 'primary', 'status_id' => 'index'],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode'        => 1,
            'fields'      => ['date'],
            'panelLayout' => 'search,limit',
        ],
        'label' => [
            'fields'      => ['date', 'status_id'],
            'format'      => '%s (%s)', // Display date and status together
        ],
        'operations' => [
            'edit' => [
                'label' => &$GLOBALS['TL_LANG']['tl_shop_closed_date']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.svg',
            ],
            'delete' => [
                'label' => &$GLOBALS['TL_LANG']['tl_shop_closed_date']['delete'],
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
        'tstamp' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'",
        ],
        'date' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_shop_closed_date']['date'],
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(19) NOT NULL default ''",
        ],
        'status_id' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_shop_closed_date']['status_id'],
            'inputType' => 'select',
            'foreignKey'=> 'shop_closed_status.id',
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "int(10) NOT NULL default '0'",
        ],
    ],
];

?>