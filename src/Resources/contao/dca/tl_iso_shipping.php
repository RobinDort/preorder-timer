<?php

// Add new field to the default palette, after 'postalCodes'
$GLOBALS['TL_DCA']['tl_iso_shipping']['palettes']['flat'] = str_replace(
    'postalCodes,',
    'postalCodes,postalCity,',
    $GLOBALS['TL_DCA']['tl_iso_shipping']['palettes']['flat']
);


// Define new postalCity the field
$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['postalCity'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_iso_shipping']['postalCity'][0],
    'exclude'   => true,
    'inputType' => 'textarea',
    'eval'      => ['tl_class' => 'clr', 'style' => 'height:100px', 'decodeEntities' => true],
    'sql'       => ['type' => 'text', 'default' => '']
];


?>