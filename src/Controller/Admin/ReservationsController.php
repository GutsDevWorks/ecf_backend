<?php 

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ReservationsRepository;

#[Route('/admin/reservations', name: 'admin_reservations_')]
class ReservationsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReservationsRepository $reservationsRepository): Response
    {
        $reservations = $reservationsRepository->findBy([]);

        return $this->render('admin/reservations/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }
}