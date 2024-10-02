<?php
use RobinDort\PreorderTimer\Backend\ProductCollection\CollectionLabelCallback;
use RobinDort\PreorderTimer\Module\IsoCollectionCallback;

$GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['label']['fields'][] = 'preorder_time';
$GLOBALS['TL_DCA']['tl_iso_product_collection']['list']['label']['label_callback'] = [CollectionLabelCallback::class, 'getOrderLabel'];


$GLOBALS['TL_DCA']['tl_iso_product_collection']['palettes']['default'] = str_replace
(
    'order_status',
    'order_status,preorder_time',
    $GLOBALS['TL_DCA']['tl_iso_product_collection']['palettes']['default']
);


$GLOBALS['TL_DCA']['tl_iso_product_collection']['fields']['preorder_time'] = [
    'label'       => &$GLOBALS['TL_LANG']['tl_iso_product_collection']['preorder_time'],
    'exclude'     => TRUE,
    'inputType'   => 'text', // Using text as it’s non-interactive
    'eval'        => [
        'rgxp'=>'datim',
        'readonly'=>false,
        'tl_class'=>'clr',
        'helpwizard'=>false,
    ],
    'load_callback' => [
        [IsoCollectionCallback::class, 'formatPreorderTime']
    ],
    'sql'=>"int(10) unsigned NULL",
];

?>  