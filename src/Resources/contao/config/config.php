<?php
use RobinDort\PreorderTimer\Backend\CheckoutStep\IsotopePreorderTime;

// Add customer notes before the last step (review).
$checkoutStepCount = count($GLOBALS['ISO_CHECKOUTSTEP']);
$length = $checkoutStepCount - 2;
$offset = $checkoutStepCount - 2;
$preorderTime = ['preorder_time' => [IsotopePreorderTime::class]];
$newCheckoutSteps = array_slice($GLOBALS['ISO_CHECKOUTSTEP'], 0, $length, TRUE) + $preorderTime + array_slice($GLOBALS['ISO_CHECKOUTSTEP'], $offset, $length, TRUE);
$GLOBALS['ISO_CHECKOUTSTEP'] = $newCheckoutSteps;


?>