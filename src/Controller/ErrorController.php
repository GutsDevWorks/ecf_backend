<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ErrorController extends AbstractController
{
    #[Route('/bundles', name: 'app_error')]
    public function bundles(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error.html.twig');
    }
}
