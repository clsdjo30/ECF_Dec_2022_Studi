<?php

namespace App\Repository;

use App\Entity\PartnerPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PartnerPermission>
 *
 * @method PartnerPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method PartnerPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method PartnerPermission[]    findAll()
 * @method PartnerPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerPermission::class);
    }

    public function save(PartnerPermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PartnerPermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


}
