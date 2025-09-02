<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{
    // Modification de la route index en route profile pour afficher à l'utilisateur sa page de profil lors de la connexion 

    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    public function profile(RoomRepository $roomRepository): Response
    {
        /** @var User $user */

        $user = $this->getUser();

        // Condition vérifiant si l'utilisateur est bien connecté pour pouvoir accéder à la page "mon profil".
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être conneté pour voir votre profil.");
        }

        // Récupération des réservations faites par l'utilisateur connecté.
        $reservations = $user->getReservations();

        // Récupération de toutes les salles pour afficher les disponibilités directement dans le profile utilisateur.

        $rooms = $roomRepository->findAll();
        $available = [];

        foreach ($rooms as $room) {
            // Récupération uniquement des réservations à venir (>= maintenant)

            $futureReservations = $room->getReservations()->filter(function ($reservation) {
                return $reservation->getStartAt() >= new \DateTime();
            })->toArray(); // toArray() : convertit la Collection Doctrine en tableau PHP normal

            // On trie les réservations par date de début (croissante)
            // $a et $b sont deux objets Reservations pris dans le tableau ( a et b sont deux réservations)
            // On compare leur date de début pour savoir lequel vient avant
            usort($futureReservations, function ($a, $b) {
                return $a->getStartAt() <=> $b->getStartAt();
            });

            $slots = []; // les créneaux libres pour la salle
            $previousEnd = new \DateTime();
            $endLimit = (new \DateTime())->modify('+1 month'); // limite l'affichage des créneaux

            if (count($futureReservations) === 0) {
                // Si la salle est entièrement libre -> un sel créneau de maintenant à plus d'un mois
                $slots[] = [
                    'start' => $previousEnd,
                    'end' => $endLimit
                ];
            } else {
                foreach ($futureReservations as $reservation) {
                    // Si le prochain début est après le dernier "previousEnd",
                    // cela veut dire qu’il y a un créneau libre entre les deux

                    if ($reservation->getStartAt() > $previousEnd) {
                        $slots[] = [
                            'start' => $previousEnd,
                            'end' => $reservation->getStartAt()
                        ];
                    }
                    // On met à jour la fin précédente avec la fin de cette réservation
                    // (si une réservation commence avant que la précédente ne soit finie,
                    // ça permet d’éviter des doublons de créneaux)

                    $previousEnd = max($previousEnd, $reservation->getEndAt());
                }

                // Après la dernièe réservation -> disponible jusqu'à +1 mois
                if ($previousEnd < $endLimit) {
                    $slots[] = [
                        'start' => $previousEnd,
                        'end' => $endLimit
                    ];
                }
            }

            // On associe les créneaux calculés à la salle dans le tableau final
            $available[$room->getName()] = $slots;
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
            'available' => $available
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    // public function show(User $user): Response
    // {
    //     return $this->render('user/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
