<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 *
 * Repository pour gérer l’entité Room.
 * Hérite des méthodes standard de Doctrine :
 *  - find($id)        → récupère une salle par son identifiant
 *  - findAll()        → récupère toutes les salles
 *  - findBy()         → récupère un tableau de salles selon des critères
 *  - findOneBy()      → récupère une salle selon des critères (ou null si aucune)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // Définit l’entité gérée par ce repository : Room
        parent::__construct($registry, Room::class);
    }

    // Exemple de méthode personnalisée générée par défaut (commentée)
    // Permettrait de rechercher des salles selon un champ fictif "exampleField"
    // Retourne un tableau d’objets Room
    //
    // public function findByExampleField($value): array
    // {
    //     return $this->createQueryBuilder('r')
    //         ->andWhere('r.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('r.id', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    // Exemple de méthode personnalisée générée par défaut (commentée)
    // Permettrait de récupérer une seule salle selon un champ fictif "exampleField"
    // Retourne un objet Room ou null
    //
    // public function findOneBySomeField($value): ?Room
    // {
    //     return $this->createQueryBuilder('r')
    //         ->andWhere('r.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
