<?php
use RobinDort\PreorderTimer\Backend\CheckoutStep\IsotopePreorderTime;
use RobinDort\PreorderTimer\Widget\Frontend\PreorderFormular;

$GLOBALS['TL_CSS'][] = "bundles/preorder-timer/flatpickr/flatpickr.css";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/preorder-timer/flatpickr/flatpickr.js";
$GLOBALS['TL_FFL']['preorder_formular'] = PreorderFormular::class;  

// Add customer notes before the last step (review).
$checkoutStepCount = count($GLOBALS['ISO_CHECKOUTSTEP']);
$insertPosition = $checkoutStepCount - 2;
$firstPart = array_slice($GLOBALS['ISO_CHECKOUTSTEP'], 0, $insertPosition, true);
$secondPart = array_slice($GLOBALS['ISO_CHECKOUTSTEP'], $insertPosition, null, true);
$preorderTime = ['preorder_time' => [IsotopePreorderTime::class]];
$newCheckoutSteps = $firstPart + $preorderTime + $secondPart;
$GLOBALS['ISO_CHECKOUTSTEP'] = $newCheckoutSteps;


?>