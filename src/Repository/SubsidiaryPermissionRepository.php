<?php

namespace App\Repository;

use App\Entity\SubsidiaryPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubsidiaryPermission>
 *
 * @method SubsidiaryPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubsidiaryPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubsidiaryPermission[]    findAll()
 * @method SubsidiaryPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubsidiaryPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubsidiaryPermission::class);
    }

    public function save(SubsidiaryPermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SubsidiaryPermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SubsidiaryPermission[] Returns an array of SubsidiaryPermission objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SubsidiaryPermission
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
