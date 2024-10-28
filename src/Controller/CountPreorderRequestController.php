<?php
namespace RobinDort\PreorderTimer\Controller;
use RobinDort\PreorderTimer\Widget\Backend\Validation\PreorderLimiter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/countPreorderRequest', name: CountPreorderRequestController::class, defaults: ['_scope' => 'frontend', '_token_check' => true])]
class CountPreorderRequestController
{
    private const MAX_AMOUNT_SHIPPING_ORDERS = 1;
    private const MAX_AMOUNT_PICK_UP_ORDERS = 2;


    public function __invoke(Request $request): JsonResponse
    {
        $extractedTime = trim($request->request->get('time'), '"');
        $extractedIsShippingOrder = trim($request->request->get('isShippingOrder'), '"');

        // $preorderLimiter = new PreorderLimiter();
        // $preorderCount = $preorderLimiter->countPreordersForDateTime($extractedTime, $extractedIsShippingOrder);

        $response = ["availablePreorderTime"=>$extractedTime];

        // if ($extractedIsShippingOrder === true && $preorderCount >= self::MAX_AMOUNT_SHIPPING_ORDERS) {
        //     $newAvailablePreorderTime = $preorderLimiter->findNextAvailableBookingTime($extractedTime, $extractedIsShippingOrder);
        //     $response["availablePreorderTime"] = $newAvailablePreorderTime;


        // } else if ($extractedIsShippingOrder === false && $preorderCount >= self::MAX_AMOUNT_PICK_UP_ORDERS) {
        //     $newAvailablePreorderTime = $preorderLimiter->findNextAvailableBookingTime($extractedTime, $extractedIsShippingOrder);
        //     $response["availablePreorderTime"] = $newAvailablePreorderTime;
        // }

        return new JsonResponse($response);
    }
}

?>