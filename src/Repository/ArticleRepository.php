<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use http\Env\Request;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Article::class);
    }

    public function paginateArticles(int $page, int $perPage, ?array $filterBy = [], ?string $sortBy = null): PaginationInterface {

        $queryBuilder = $this->createQueryBuilder('a')
            ->leftJoin('a.categories', 'c') // Jointure entre article et categorie
            ->addSelect('c');

        if (!empty($filterBy)) {
            $queryBuilder->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $filterBy);
        }

        switch ($sortBy) {
            case 'title_asc':
                $queryBuilder->orderBy('a.title', 'ASC');
                break;
            case 'title_desc':
                $queryBuilder->orderBy('a.title', 'DESC');
                break;
            case 'price_asc':
                $queryBuilder->orderBy('a.price', 'ASC');
                break;
            case 'price_desc':
                $queryBuilder->orderBy('a.price', 'DESC');
                break;
            default:
                $queryBuilder->orderBy('a.createdAt', 'DESC');
        }

        return $this->paginator->paginate(
            $queryBuilder->getQuery(),
            $page,
            $perPage,
        );
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
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

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
