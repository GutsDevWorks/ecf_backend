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
//use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/room')]
final class RoomController extends AbstractController
{
    #[Route(name: 'app_room_index', methods: ['GET'])]
    public function index(RoomRepository $roomRepository, Request $request): Response
    {
        // Récupération de la capacité
        $capacity = $request->query->get('capacity');
        $capacity = is_numeric($capacity) ? (int)$capacity : null;

        // Récupération des options (checkboxes)
        $types = $request->query->all('type'); // toujours un tableau
        if (!is_array($types)) {
            $types = [];
        }

        $qb = $roomRepository->createQueryBuilder('r');

        // Filtre par capacité
        if ($capacity !== null) {
            $qb->andWhere('r.capacity >= :capacity')
               ->setParameter('capacity', $capacity);
        }

        // Filtre par options
        if (!empty($types)) {
            $qb->join('r.options', 'o')
               ->andWhere('o.name IN (:types)')
               ->setParameter('types', $types)
               ->groupBy('r.id')
               ->having('COUNT(DISTINCT o.id) = :typesCount')
               ->setParameter('typesCount', count($types));
        }

        $rooms = $qb->getQuery()->getResult();

        return $this->render('room/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/new', name: 'app_room_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Juste admin peut créer un salle
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_room_show', methods: ['GET'])]
    public function show(Room $room): Response
    {
        return $this->render('room/show.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_room_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] // Juste admin peut modifier un salle
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_room_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')] // Juste admin peut supprimer un salle
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_room_index', [], Response::HTTP_SEE_OTHER);
    }
}
