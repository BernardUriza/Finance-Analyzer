<?php

namespace App\Controller;

use App\Repository\FinancialProfileRepository;
use App\Service\CalculationEngine;
use App\Service\InsightsEngine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        FinancialProfileRepository $repository,
        CalculationEngine $calculationEngine,
        InsightsEngine $insightsEngine,
    ): Response {
        $profile = $repository->getOrCreate();
        $calculations = $calculationEngine->calculate($profile);
        $insights = $insightsEngine->analyze($profile, $calculations);

        return $this->render('dashboard/index.html.twig', [
            'profile' => $profile,
            'calc' => $calculations,
            'insights' => $insights,
        ]);
    }
}
