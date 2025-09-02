<?php

namespace App\Repository;

use App\Entity\Options;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Options>
 *
 * Ce repository permet d’interagir avec l’entité Options via Doctrine.
 * Hérite des méthodes standards : find, findAll, findBy, findOneBy...
 */
class OptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // Appelle le constructeur parent en précisant l’entité gérée : Options
        parent::__construct($registry, Options::class);
    }

    // Pagination des résultats pour les options
    public function paginateOptions(int $page, int $limit, QueryBuilder $qb): Paginator
    {
        $query = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit) // limite le nombre de résultats par page
            ->getQuery()
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false); // évite les doublons avec les jointures  
        return new Paginator($query); 
    }
    // Exemple de méthode personnalisée générée par défaut (commentée)
    // Permet de récupérer une liste d’Options en fonction d’un champ fictif "exampleField"
    // Retourne un tableau d’objets Options
    //
    // public function findByExampleField($value): array
    // {
    //     return $this->createQueryBuilder('o')
    //         ->andWhere('o.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('o.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // Exemple de méthode personnalisée pour récupérer un seul objet Options
    // en fonction d’un champ fictif "exampleField"
    // Retourne soit un objet Options, soit null
    //
    // public function findOneBySomeField($value): ?Options
    // {
    //     return $this->createQueryBuilder('o')
    //         ->andWhere('o.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
