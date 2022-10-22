<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;


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
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {

        parent::__construct($registry, Partner::class);
        $this->paginator = $paginator;
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
     * Récupère les produits en lien avec une recherche
     * @param SearchData $search
     * @return PaginationInterface
     */
    public function findPartnerBySearch(SearchData $search ): PaginationInterface
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
                ->andWhere('p.isActive = 1')
                ;
        }



        return $this->paginator->paginate(
            $query,
            $search->page,
            6
        );
    }

}
