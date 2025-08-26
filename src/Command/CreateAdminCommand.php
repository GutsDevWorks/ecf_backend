<?php

// Création d'une commande permettant d'ajouter un administrateur en base de données 

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Admin creation command',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private  EntityManagerInterface $em,
        private  UserPasswordHasherInterface $passwordHasher

    )
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('firstname', InputArgument::REQUIRED, 'Admin firstname (required)')
            ->addArgument('lastname', InputArgument::REQUIRED, 'Admin lastname (required)')
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email (required)')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password (required)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $firstname = $input->getArgument('firstname');
        $lastname = $input->getArgument('lastname');
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');

        // Création de l'utilisateur

        $admin = new User();
        $admin->setFirstname($firstname);
        $admin->setLastname($lastname);
        $admin->setEmail($email);
        $admin->setRoles(['ROLE_ADMIN']); // Ajout automatique du Rôle Administrateur
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $plainPassword));

        // Ajout en base de données

        $this->em->persist($admin);
        $this->em->flush();

        $io->success("L'administrateur à été crée avec succès : $firstname ");

        return Command::SUCCESS;
    }
}
