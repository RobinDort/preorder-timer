<?php
namespace RobinDort\PreorderTimer\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/removeClosedDayEntry', name: SpecialClosedDaysRequestController::class, defaults: ['_token_check' => true])]
class SpecialClosedDaysRequestController {
    public function __invoke(Request $request): JsonResponse {
        $entryDate = $request->request-get('entryDate');
        $entryStatus = $request->request-get('entryStatus');

        \System::log("request data: " . $entryDate . "," . $entryStatus,__METHOD__,"TL_ERROR");
        throw new \Exception("DEBUG");
    }
}
?>