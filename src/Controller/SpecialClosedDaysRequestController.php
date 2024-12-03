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
        $affectedRows = $preorderStatusInteractor->deleteNormalShopClosingDay($entryDate, $entryStatus);

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

        $preorderStatusInteractor = new PreorderStatusInteractor();

        $response = $preorderStatusInteractor->insertNormalClosedShopDay($date,$status);
            if ($response["success"] === true) {
                return new JsonResponse(['status' => 'success', 'message' => $response["message"]]);
                
            } else {
                return new JsonResponse(['status' => 'error', 'message' => $response["message"]]);
            }
    }

    #[Route('/addSpecialClosedDayEntry', name: 'add_special_closed_day_entry', defaults: ['_token_check' => true, '_scope' => 'backend'],  methods: ['POST'])]
    public function addSpecialClosedDayEntry(Request $request): JsonResponse {
        $selectedTimes =  $request->request->get('times');
        $date = $request->request->get('date');

        if (!$selectedTimes || !$date) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid data provided'], 400);
        }

        $preorderStatusInteractor = new PreorderStatusInteractor();

        $response = $preorderStatusInteractor->insertSpecialClosedShopDay($date, $selectedTimes);
        if ($response["success"] === true) {
            return new JsonResponse(['status' => 'success', 'message' => $response["message"]]);
            
        } else {
            return new JsonResponse(['status' => 'error', 'message' => $response["message"]]);
        }
    }
}
?>