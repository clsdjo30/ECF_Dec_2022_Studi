<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Partner>
 *
 * @method Partner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partner[]    findAll()
 * @method Partner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    public function save(Partner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Partner $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Partner[] Returns an array of Subsidiary objects
     */
    public function findByUser($id): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.subsidiaries', 'u' )
            ->from('user', 'u')
            ->where('p.user = :u.id')
            ->setParameter('u.id', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param SearchData $search
     * @return array
     */
    public function findPartnerBySearch(SearchData $search ): array
    {

        $query = $this
            ->createQueryBuilder('p')
        ;

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q ')
                ->setParameter('q', "%{$search->q}%")
                ;
        }
        
        if (!empty($search->active)) {
            $query = $query
                ->where('p.isActive IN (:isActive)')
                ->setParameter('isActive', $search->active)
                ;
        }

        if (!empty($search->close)) {
            $query = $query
                ->where('p.isActive = 0')
                ;
        }

        return $query->getQuery()->getResult();
    }

}
