<?php
use RobinDort\PreorderTimer\Backend\CheckoutStep\IsotopePreorderTime;
use RobinDort\PreorderTimer\Widget\Frontend\PreorderFormular;

$GLOBALS['TL_CSS'][] = "bundles/robindortpreordertimer/css/flatpickr.css|static";
$GLOBALS['TL_CSS'][] = "bundles/robindortpreordertimer/css/preorder.css|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpreordertimer/js/flatpickr.js|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpreordertimer/js/flatpickr_de.js|static";



$GLOBALS['FE_MOD']['isotope']['iso_orderdetails'] = 'RobinDort\PreorderTimer\Module\OrderDetails';
$GLOBALS['TL_FFL']['preorder_formular'] = PreorderFormular::class;  

// Add customer notes before the last step (review).
$checkoutStepCount = count($GLOBALS['ISO_CHECKOUTSTEP']);
$insertPosition = $checkoutStepCount - 2;

$firstPart = array_slice($GLOBALS['ISO_CHECKOUTSTEP'], 0, $insertPosition, true);
$secondPart = array_slice($GLOBALS['ISO_CHECKOUTSTEP'], $insertPosition, null, true);
$preorderTime = ['preorder_time' => [IsotopePreorderTime::class]];

$newCheckoutSteps = array_merge($firstPart, $preorderTime, $secondPart);
$GLOBALS['ISO_CHECKOUTSTEP'] = $newCheckoutSteps;


?>