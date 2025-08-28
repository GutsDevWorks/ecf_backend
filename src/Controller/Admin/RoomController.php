<?php 

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Room;
use App\Form\RoomType;

#[Route('/admin/room', name: 'admin_room_')]
class RoomController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findBy([]);

        return $this->render('admin/room/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($room);
            $em->flush();

            return $this->redirectToRoute('admin_room_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/room/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);



    }
}