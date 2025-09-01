<?php 

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ReservationsRepository;
use App\Entity\Reservations;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/admin/reservations', name: 'admin_reservations_')]
class ReservationsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReservationsRepository $reservationsRepository): Response
    {
        // Récupération de toutes les réservations, triées par date de création décroissante.

        $reservations = $reservationsRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/reservations/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    // Récupération des réservations avec le statut "en attente"
    #[Route('/pending', name:'pending')]
    public function pendingReservationsList(ReservationsRepository $reservationsRepository): Response
    {
        $pendingReservations = $reservationsRepository->findByStatus(null);

        return $this->render('admin/reservations/pending_reservation.html.twig', [
            "pendingReservations" => $pendingReservations
        ]);

    }

    // Validation d'une pré-réservation (passe reservationStatus à true et ajoute la date de validation). 

    #[Route('/{id}/validate', name: 'validate', methods: ['POST'])]
    public function validate(Reservations $reservation, EntityManagerInterface $entityManager): RedirectResponse
    {
        $reservation->setReservationStatus(true); // Mise à jour du statut
        $reservation->setValidatedAt(new \DateTimeImmutable()); // Ajout de la date de validation
        $entityManager->flush(); // Enregistrement en BDD

        $this->addFlash('success', 'La réservation a été validée avec succès !');
        return $this->redirectToRoute('admin_reservations_index');
    }

    // Refus d'une pré-réservation 

    #[Route('/{id}/refuse', name: 'refuse', methods: ['POST'])]
    public function refuse(Reservations $reservation, EntityManagerInterface $entityManager): RedirectResponse
    {
        $reservation->setReservationStatus(false); // Mise à jour du statut
        $entityManager->flush(); // Enregistrement en BDD

        $this->addFlash('warning', 'Réservation refusée.');
        return $this->redirectToRoute('admin_reservations_index');
    }
}