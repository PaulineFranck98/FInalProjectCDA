<?php

namespace App\Repository;

use App\Entity\Itinerary;
use App\Entity\ItineraryLocation;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ItineraryLocation>
 */
class ItineraryLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItineraryLocation::class);
    }

    public function getNextOrderIndex(Itinerary $itinerary): int 
    {
        $lastOrder = $this->createQueryBuilder('il')
            ->select('MAX(il.orderIndex)')
            ->where('il.itinerary = :itinerary')
            ->setParameter('itinerary', $itinerary)
            ->getQuery()
            ->getSingleScalarResult();
        
            return $lastOrder !== null ? ((int)$lastOrder + 1) : 0;
    }

//    /**
//     * @return ItineraryLocation[] Returns an array of ItineraryLocation objects
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

//    public function findOneBySomeField($value): ?ItineraryLocation
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
