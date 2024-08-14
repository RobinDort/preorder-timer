<?php
use Isotope\Model\OrderStatus;
use RobinDort\PreorderTimer\Backend\CheckoutStep\IsotopePreorderTime;
use RobinDort\PreorderTimer\Widget\Frontend\PreorderFormular;
use RobinDort\PreorderTimer\Model\PreorderStatus;
use RobinDort\PreorderTimer\EventListener\Isotope\PreOrderStatusUpdateListener;
use RobinDort\PreorderTimer\Notification\PreorderTimeTokenProvider;

/**
 * Set all the public resources for javascript files and css files.
 */
$GLOBALS['TL_CSS'][] = "bundles/robindortpreordertimer/css/flatpickr.css|static";
$GLOBALS['TL_CSS'][] = "bundles/robindortpreordertimer/css/preorder.css|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpreordertimer/js/flatpickr.js|static";
$GLOBALS['TL_JAVASCRIPT'][] = "bundles/robindortpreordertimer/js/flatpickr_de.js|static";


// Load the custom frontend module for the order details.
$GLOBALS['FE_MOD']['isotope']['iso_orderdetails'] = 'RobinDort\PreorderTimer\Module\OrderDetails';

// Init the widgets class for the preorder formular. 
$GLOBALS['TL_FFL']['preorder_formular'] = PreorderFormular::class;  

// Init the new preorder status for the backend and save the new status into the database.
$preorderStatus = new OrderStatus();

// First check if entry already exists
$existingStatus = OrderStatus::findBy('name', 'Vorbestellung');

// Create a new order status with sorting 312. (Sorting can be changed according to the users database sorting order) 
if ($existingStatus === null) {
    $preorderStatus->name = "Vorbestellung";
    $preorderStatus->color = "ff00ff";
    $preorderStatus->tstamp = time();
    $preorderStatus->sorting = 312;
    $preorderStatus->save();
}

// Add customer notes before the last step (review).
$checkoutStepCount = count($GLOBALS['ISO_CHECKOUTSTEP']);
$insertPosition = $checkoutStepCount - 2;

$firstPart = array_slice($GLOBALS['ISO_CHECKOUTSTEP'], 0, $insertPosition, true);
$secondPart = array_slice($GLOBALS['ISO_CHECKOUTSTEP'], $insertPosition, null, true);
$preorderTime = ['preorder_time' => [IsotopePreorderTime::class]];

$newCheckoutSteps = array_merge($firstPart, $preorderTime, $secondPart);
$GLOBALS['ISO_CHECKOUTSTEP'] = $newCheckoutSteps;


// Update the order status and call the hook to preorder when a preorder_time has been set
$GLOBALS['ISO_HOOKS']['preOrderStatusUpdate'][] = [PreOrderStatusUpdateListener::class, '__invoke'];

// Init a new notification center token that provides the preorder_time so it can be attached to the billings.
$GLOBALS['TL_HOOKS']['getOrderNotificationTokens'][] = [PreorderTimeTokenProvider::class,'__invoke'
];
?>