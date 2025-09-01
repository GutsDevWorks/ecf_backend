<?php 

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ReservationsRepository;
use App\Entity\Reservations;
use DateTimeImmutable;

#[Route('/admin', name: 'admin_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReservationsRepository $reservationsRepository): Response
    {
        // Ajout de la logique de notification (date cible 5 jours avant)

        $targetDate = (new DateTimeImmutable())->modify('+5 days'); // Créer un nouvel objet DateTimeImmutable qui représente la date dans 5 jours en fonction de la date actuelle. L'objet initial n'est pas modifié.

        // Récupération des réservations en attente dont la date est dans 5 jours. reminder est la fonction définie dans le ReservationsRepository
        $reminders = $reservationsRepository->reminder($targetDate);
        
        return $this->render('admin/index.html.twig', [
            'reminders' => $reminders,
        ]);
    }
}