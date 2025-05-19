<?php

// Add new field to the default palette, after 'postalCodes'
$GLOBALS['TL_DCA']['tl_iso_shipping']['palettes']['default'] = str_replace(
    'postalCodes,',
    'postalCodes,postalCity,',
    $GLOBALS['TL_DCA']['tl_iso_shipping']['palettes']['default']
);


// Define new postalCity the field
$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['postalCity'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_iso_shipping']['postalCity'],
    'exclude'   => true,
    'inputType' => 'textarea',
    'eval'      => ['tl_class' => 'clr', 'style' => 'height:100px', 'decodeEntities' => true],
    'sql'       => ['type' => 'text', 'default' => '']
];


?>