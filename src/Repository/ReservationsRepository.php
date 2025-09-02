<?php

namespace App\Repository;

use DateTimeImmutable;
use App\Entity\Reservations;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Reservations>
 */
class ReservationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservations::class);
    }

    // Création  d'une fonction récupérant les réservations en fonction de leur statut. Retourne un tableau.
    public function findByStatus(?bool $status): array
    {
        $queryBuilder = $this->createQueryBuilder('reservation');

        if($status === null) {
            // Cas dans lequel le statut est null en BDD soit "en attente".
            $queryBuilder->andWhere('reservation.reservationStatus IS NULL');
        } else {
            // Cas d'égalité stricte sur TRUE ou FALSE 
            $queryBuilder->andWhere('reservation.reservationStatus = :status')
            ->setParameter('status', $status);
        }

        return $queryBuilder->orderBy('reservation.createdAt', 'DESC')
        ->getQuery()
        ->getResult();
    }

    // Création d'un query builder permettant de récupérer les réservations dont le status est null (en attente) et dont la date de début est dans 5 jours à compter d ela date du jour. Retourne un tableau.
    // Attention : DATE() est spécifique à MySQL. Sur PostgreSQL/SQLite il faudra adapter (ex: utiliser une comparaison directe avec BETWEEN).

    public function reminder(DateTimeImmutable $date): array
    {
        $startOfDay = $date->setTime(0,0,0);
        $nextDay = $date->modify('+1 day');

        return $this->createQueryBuilder('reservation')
        ->andWhere('reservation.reservationStatus IS NULL') // Seulement les réservations en attente
        ->andWhere('reservation.startAt >= :startOfDay AND reservation.startAt < :endOfDay') // Dont la date de début correspond à $date
        ->setParameter('startOfDay', $startOfDay)
        ->setParameter('endOfDay', $nextDay)
        ->orderBy('reservation.startAt', 'ASC') // Tri par date de début croissante
        ->getQuery()
        ->getResult();
    }

     // Pagination des résultats pour les salles
    public function paginateReservations(int $page, int $limit): Paginator
    {
        return new Paginator($this
            ->createQueryBuilder('r')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit) // limite le nombre de résultats par page
            ->getQuery()
    );
        }
    //    /**
    //     * @return Reservations[] Returns an array of Reservations objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservations
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
