<?php
namespace RobinDort\PreorderTimer\Controller;
use RobinDort\PreorderTimer\Widget\Frontend\Helper\HolidayCalculation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/holidayRequest', name: HolidayRequestController::class, defaults: ['_scope' => 'frontend', '_token_check' => true])]
class HolidayRequestController
{
    public function __invoke(Request $request): Response
    {
        $extractedDate = $request->request->get('date');

        return new Response($extractedDate);
    }
}

?>