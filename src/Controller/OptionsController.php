<?php

namespace App\Controller;

use App\Entity\Options;
use App\Form\OptionsType;
use App\Repository\OptionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/options')]
#[IsGranted('ROLE_ADMIN')] // Option : autoriser uniquement les admins
final class OptionsController extends AbstractController
{
    #[Route(name: 'app_options_index', methods: ['GET'])]
    public function index(OptionsRepository $optionsRepository, Request $request): Response
    {
        // Récupération des filtres envoyés via l'URL (GET)
        $name = $request->query->get('name');
        $type = $request->query->get('type');

        // Construction d'une requête dynamique avec QueryBuilder
        $qb = $optionsRepository->createQueryBuilder('o');

        // Filtre par nom si présent
        if ($name) {
            $qb->andWhere('o.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        // Filtre par type si présent
        if ($type) {
            $qb->andWhere('o.type LIKE :type')
                ->setParameter('type', '%' . $type . '%');
        }

        // Exécution de la requête et récupération des résultats
        $options = $qb->getQuery()->getResult();

        // Pagination
        $page = $request->query->getInt('page', 1);
        $limit = 5; //Nombre salle par page
        $options = $optionsRepository->paginateOptions($page, $limit, $qb);
        $maxPage = ceil(count($options) / $limit);

        // Passage des résultats au template Twig
        return $this->render('options/index.html.twig', [
            'options' => $options,
            'page' => $page,
            'maxPage' => $maxPage,
        ]);
    }

    #[Route('/new', name: 'app_options_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle option
        $option = new Options();

        // Génération du formulaire lié à l'entité
        $form = $this->createForm(OptionsType::class, $option);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide → enregistrement en BDD
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($option);
            $entityManager->flush();

            return $this->redirectToRoute('app_options_index', [], Response::HTTP_SEE_OTHER);
        }

        // Sinon on affiche le formulaire
        return $this->render('options/new.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_options_show', methods: ['GET'])]
    public function show(Options $option): Response
    {
        // Affichage d'une option précise
        return $this->render('options/show.html.twig', [
            'option' => $option,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_options_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Options $option, EntityManagerInterface $entityManager): Response
    {
        // Formulaire d’édition lié à l'entité Options
        $form = $this->createForm(OptionsType::class, $option);
        $form->handleRequest($request);

        // Si formulaire soumis et valide → mise à jour en BDD
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_options_index', [], Response::HTTP_SEE_OTHER);
        }

        // Sinon affiche le formulaire d’édition
        return $this->render('options/edit.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_options_delete', methods: ['POST'])]
    public function delete(Request $request, Options $option, EntityManagerInterface $entityManager): Response
    {
        // Vérifie le token CSRF avant suppression pour éviter attaques
        if ($this->isCsrfTokenValid('delete' . $option->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($option);
            $entityManager->flush();
        }

        // Redirection après suppression vers la liste
        return $this->redirectToRoute('app_options_index', [], Response::HTTP_SEE_OTHER);
    }
}
