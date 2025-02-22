<?php

namespace App\Repository;

use App\Entity\Ad;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ad>
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory($categoryId): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->orderBy('a.created_at', 'DESC');

        if ($categoryId !== 'all') {
            $queryBuilder->andWhere('a.category = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        return $queryBuilder->getQuery()->getResult();
    }


    public function search(string $query): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c') // Jointure avec la catégorie
            ->andWhere('a.title LIKE :query')
            ->orWhere('a.description LIKE :query')
            ->orWhere('c.name LIKE :query') // Si la catégorie a un champ "name"
            ->setParameter('query', "%$query%")
            ->orderBy('a.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Ad[] Returns an array of Ad objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ad
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
