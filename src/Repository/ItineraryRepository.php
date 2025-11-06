<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Itinerary;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Itinerary>
 */
class ItineraryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Itinerary::class);
    }

    public function findLastByUser(User $user, int $limit = 2): array
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.users', 'u')
            ->andWhere('u = :user')
            ->setParameter('user', $user)
            ->orderBy('i.creationDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingByUser(User $user, int $limit = 2): array
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.users', 'u')
            ->andWhere('u = :user')
            ->andWhere('i.departureDate > :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('i.departureDate', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    public function countCreatedAfter(\DateTimeInterface $date): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.creationDate >= :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCreatedBetween(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.creationDate >= :start')
            ->andWhere('i.creationDate < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function findPublicItineraries(?int $duration = null, string $sort = 'recent'): array
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.isPublic = true');

        if ($duration !== null) {
            $qb->andWhere('i.duration = :duration')
            ->setParameter('duration', $duration);
        }

        $orderDirection = $sort === 'old' ? 'ASC' : 'DESC';
        $qb->orderBy('i.creationDate', $orderDirection);

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Itinerary[] Returns an array of Itinerary objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Itinerary
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
