<?php 

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OptionsRepository;
use App\Entity\Options;
use App\Form\OptionsType;

#[Route('/admin/options', name: 'admin_options_')]
class OptionsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(OptionsRepository $optionsRepository): Response
    {
        $options = $optionsRepository->findBy([]);

        return $this->render('admin/options/index.html.twig', [
            'options' => $options,
        ]);
    }

    // #[Route('/new', name:'new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $option = new Options();
    //     $form = $this->createForm(OptionsType::class, $option);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($option);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('admin_options_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('admin/options/new.html.twig', [
    //         'option' => $option,
    //         'form' => $form,
    //     ]);
    // }
}