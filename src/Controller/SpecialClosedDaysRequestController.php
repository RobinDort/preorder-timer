<?php
namespace RobinDort\PreorderTimer\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use RobinDort\PreorderTimer\Backend\Validation\PreorderStatusInteractor;

class SpecialClosedDaysRequestController {

    #[Route('/removeClosedDayEntry', name: 'remove_closed_day_entry', defaults: ['_token_check' => true, '_scope' => 'backend'],  methods: ['POST'])]
    public function removeClosedDayEntry(Request $request): JsonResponse {
        $entryDate = $request->request->get('entryDate');
        $entryStatus = $request->request->get('entryStatus');

        // Check if the parameters are set
        if (!$entryDate || !$entryStatus) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid data provided'], 400);
        }

        $preorderStatusInteractor = new PreorderStatusInteractor();
        $affectedRows = $preorderStatusInteractor->deleteSpecialClosedDay($entryDate, $entryStatus);

        if ($affectedRows === 0) {
            return new JsonResponse(['status' => 'failure', 'message' => 'No entry found for date: ' . $entryDate . ' and status: ' . $entryStatus]);
        } else {
            return new JsonResponse(['status' => 'success', 'message' => 'Row successfully deleted']);
        }

    }

    #[Route('/addClosedDayEntry', name: 'add_closed_day_entry', defaults: ['_token_check' => true, '_scope' => 'backend'],  methods: ['POST'])]
    public function addClosedDayEntry(Request $request): JsonResponse {
        $date = $request->request->get('date');
        $status = $request->request->get('status');

         // Check if the parameters are set
         if (!$date || !$status) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid data provided'], 400);
        }

        return new JsonResponse(['status' => 'success', 'message' => 'Row successfully inserted']);
    }
}
?>