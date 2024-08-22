<?php
namespace RobinDort\PreorderTimer\Controller;
use RobinDort\PreorderTimer\Widget\Frontend\Helper\HolidayCalculation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/holidayRequest', name: HolidayRequestController::class, defaults: ['_scope' => 'frontend', '_token_check' => true])]
class HolidayRequestController
{
    public function __invoke(Request $request): JsonResponse
    {
        $extractedDate = trim($request->request->get('date'), '"');
        $holidayHelper = new HolidayCalculation();
        $response = ["isHoliday"=>false];

        if ($holidayHelper->isHolidayForDate($extractedDate) === 1) {
            $response["isHoliday"] = true;
        }

        return new JsonResponse($response);
    }
}

?>