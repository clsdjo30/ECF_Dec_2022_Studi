<?php

namespace App\Controller;

use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(PartnerRepository $partnerRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'partners' => $partnerRepository->findAll()
        ]);
    }
}
