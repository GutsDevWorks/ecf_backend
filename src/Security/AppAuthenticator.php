<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface; // Permet de générer des URLs à partir des noms d eroutes
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface; // Représente l'utilisateur authentifié et ses rôles après login
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator; // Classe abstraite pour créer un authenticator basé sur un formulaire
use Symfony\Component\Security\Http\Authenticator\Passport\Passport; // Représente l'objet 'Passport' utilisé par Symfony pour l'authentification
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials; // Pour vérifier le mot de passe
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge; // Permet à Symfony de retrouver l'utilisateur via son identifiant (username, email etc)
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response; 


// Mis ene place d'un authenticator pour gérer la connexion via forulaire et redirecton selon le rôle

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;

    }

    // Méthode permettant de récupérer l'email fourni dans le formulaie pour le traitement de l'authentification

    public function authenticate(Request $request): Passport
    {
        // Récupération de l'adresse email 
        $email = $request->request->get('_username', '');
        // Récupération du mot de passe
        $password = $request->request->get('_password', '');

        // Création d'un passport auto-validant avec l'identifiant. 
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }

    // Méthode retournant l'URL de la page d elogin. Si un utilisateur non authentifié tente d'accéder à uen page protégée, il sera redirigé ici

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }

    // Méthode appelée après uen connexion réussie. Gestion de la redirection selon le rôle (admmin/user)

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $roles = $token->getRoleNames();

        // Si l'utilisateur est un admin, redirection vers le tableau de bord de l'administrateur

        if(in_array('ROLE_ADMIN', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('admin_index'));
        }

        // Sinon, redirige vers la page de profil des utilisateurs normaux
        return new RedirectResponse($this->urlGenerator->generate('app_user_profile'));
    }

}