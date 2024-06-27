<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function fetchPaginatedProduct(
        int $page,
        int $limit,
        string $order,
        ?string $search,
    ): array
    {
        $queryBuilder = $this->createQueryBuilder('e');

        if ($search) {
            $queryBuilder->andWhere('e.name LIKE :search OR e.description LIKE :search')
                ->setParameter('search', "%$search%");
        }

        $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('e.id', $order);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
