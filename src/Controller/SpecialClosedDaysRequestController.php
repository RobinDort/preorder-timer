<?php
namespace RobinDort\PreorderTimer\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/removeClosedDayEntry', name: SpecialClosedDaysRequestController::class, defaults: ['_token_check' => true, '_scope' => 'backend'],  methods: ['POST'])]
class SpecialClosedDaysRequestController {
    public function __invoke(Request $request): JsonResponse {
        $entryDate = $request->request->get('entryDate');
        $entryStatus = $request->request->get('entryStatus');

        // Check if the parameters are set
        if (!$entryDate || !$entryStatus) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid data provided'], 400);
        }

        return new JsonResponse(['status' => 'success', 'message' => 'Entry received']);
    }
}
?>