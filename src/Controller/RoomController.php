<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\OptionsRepository;

#[Route('/room')]
final class RoomController extends AbstractController
{
    #[Route(name: 'app_room_index', methods: ['GET'])]
    public function index(RoomRepository $roomRepository, Request $request, OptionsRepository $optionsRepository): Response
    {

        // Récupère toutes les options disponibles (pour les filtres)
        $options = $optionsRepository->findAll();

        // Récupère le paramètre "capacity" dans l'URL (GET), si numérique on le convertit en entier
        $capacity = $request->query->get('capacity');
        $capacity = is_numeric($capacity) ? (int)$capacity : null;

        // Récupère tous les paramètres "type" dans l'URL, sinon tableau vide
        $types = $request->query->all('type');
        if (!is_array($types)) {
            $types = [];
        }

        // Si la capacité est négative -> on affiche aucune salle et on envoie un message d’erreur
        if ($capacity !== null && $capacity < 0) {
            $this->addFlash('error', 'Veuillez saisir un nombre positif');

            return $this->render('room/index.html.twig', [
                'rooms' => [], // aucune salle affichée
            ]);
        }

        // Création d'un QueryBuilder sur l'entité Room
        $qb = $roomRepository->createQueryBuilder('r');

        // Filtrer les salles par capacité minimale si précisé
        if ($capacity !== null) {
            $qb->andWhere('r.capacity >= :capacity')
                ->setParameter('capacity', $capacity);
        }

        // Filtrer les salles par types d’options si précisé
        if (!empty($types)) {
            $qb->join('r.options', 'o')
                ->andWhere('o.name IN (:types)')
                ->setParameter('types', $types)
                ->groupBy('r.id')
                ->having('COUNT(DISTINCT o.id) = :typesCount') // Vérifie que toutes les options sont présentes. :typesCount doit être de la même valeur que $types.
                ->setParameter('typesCount', count($types));
        }

        // Exécution de la requête et récupération des salles
        // $rooms = $qb->getQuery()->getResult();

        // Pagination
        $page = $request->query->getInt('page', 1);
        $limit = 5; //Nombre salle par page
        $rooms = $roomRepository->paginateRoom($page, $limit, $qb);
        $maxPage = ceil(count($rooms) / $limit);

        // Si la capacité est négative -> on affiche aucune salle et on envoie un message d’erreur
        if ($capacity !== null && $capacity < 0) {
            $this->addFlash('error', 'Veuillez saisir un nombre positif');
            return $this->render('room/index.html.twig', [
            'rooms' => [],
            'page' => $page,
            'maxPage' => $maxPage,
            'options' => $options, // Pour afficher les filtres
            ]);
        }
        // Affichage dans le template
            return $this->render('room/index.html.twig', [
            'rooms' => $rooms,
            'page' => $page,
            'maxPage' => $maxPage,
            'options' => $options, // Pour afficher les filtres
        ]);
    }

    #[Route('/new', name: 'app_room_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Option : autoriser uniquement les admins
    public function new(Request $request, EntityManagerInterface $entityManager, RoomRepository $roomRepository): Response
    {
        // Vérifie si l'utilisateur est admin, sinon redirection vers une page d'erreur
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     return $this->redirectToRoute('app_error');
        // }
        
        // Création d'un nouvel objet Room
        $room = new Room();
        // Création du formulaire lié à l'entité
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        // Si formulaire soumis et valide -> on persiste et on enregistre en BDD
        if ($form->isSubmitted() && $form->isValid()) {

            // Persistance de la salle afin d'obtenir l'ID pour gérer l'image
            $entityManager->persist($room);
            $entityManager->flush();

            // Récupérer le fichier envoyé par le formulaire (champ "photo")
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $photoFileName = $room->getId() . '.' . $photoFile->getClientOriginalExtension();
                // Déplacer le fichier dans le dossier public/room/img
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/room/img',
                    $photoFileName
                );
                // Mise à jour du nom de fichier en BDD
                $room->setPhoto($photoFileName);
                $entityManager->flush();
            }

            if ($request->isXmlHttpRequest()) {
                $rooms = $roomRepository->findAll();
                return $this->render('room/_liste_admin.html.twig', [
                    'rooms' => $rooms
                ]);
            }

            // Redirection vers la liste des salles
            return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
        }

        // Condition permettant d'afficher une vue partielle directement dans le dashboard de l'admin.
        // Si on utilise le lien classique /room/new il affichera le template complet (le code juste en dessous).

        if ($request->isXmlHttpRequest()) {
            return $this->render('room/_form_admin_container.html.twig', [
                'room' => $room,
                'form' => $form->createView()
            ]);
        }

        // Sinon, on affiche le formulaire
        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_room_show', methods: ['GET'])]
    public function show(Room $room): Response
    {
        // Affiche les détails d'une salle
        return $this->render('room/show.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_room_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire d’édition
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        // Si formulaire soumis et valide -> on met à jour la salle
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier envoyé par le formulaire (champ "photo")
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $photoFileName = $room->getId() . '.' . $photoFile->getClientOriginalExtension();
                // Déplacer le fichier dans le dossier public/room/img
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/room/img',
                    $photoFileName
                );

                // Mise à jour du nom de fichier en BDD
                $room->setPhoto($photoFileName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affiche le formulaire d’édition
        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_room_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        // Vérifie le token CSRF avant suppression
        if ($this->isCsrfTokenValid('delete' . $room->getId(), $request->request->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
        }

        // Redirection vers la liste après suppression
        return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
    }
}
