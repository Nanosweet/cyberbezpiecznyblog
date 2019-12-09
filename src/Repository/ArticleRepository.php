<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
     * Funkcja wybierajaca z bazy artykuly udostepnione w ciągu ostatnich trzech dni */
    public function findAllPublishedLastThreeDays()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt > CURRENT_DATE() - 3')
            ->andWhere('a.reported != TRUE')
            ->orderBy('a.publishedAt', 'DESC');
    }

    /*
     * Funkcja wybierajaca z bazy wszystkie artykuly, wyswietlajac je od najnowszego */
    public function findAllPublishedByNewest()
    {
        return $this -> createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.reported != TRUE')
            ->orderBy('a.publishedAt', 'DESC');
    }

    public function findAllPublishedByTitle(?string $term)
    {
        $qb = $this -> createQueryBuilder('a');

        if ($term) {
            $qb -> andWhere('a.title LIKE :term')
                -> andWhere('a.reported != TRUE')
                -> setParameter('term', '%' . $term . '%');
        }

        return $qb
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
