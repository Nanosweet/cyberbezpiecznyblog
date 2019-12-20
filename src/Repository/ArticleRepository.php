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


    // ===== PANEL ADMINISTRATORA =====
    /*
     * src/Controller/Admin/AdminController
     * Artykuły dodane w ciągu ostatnich 3 dni
     * Nie są zgłoszone
     * Nie są usunięte
     * Posortowane od najnowszego
     */

    public function findAllPublishedRecently()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt > CURRENT_DATE() - 3')
            ->andWhere('a.reported != TRUE')
            ->andWhere('a.isDeleted != TRUE')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /*
     * src/Controller/Admin/AdminController
     * Wszystkie artykuły
     * Nie są zgłoszone
     * Nie sa usunięte
     * Posortowane od najnowszego
     */

    public function findAllPublishedNonDeletedNonReported()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.reported != TRUE')
            ->andWhere('a.isDeleted != TRUE')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /*
     * /src/Controller/Admin/AdminController
     * Wyswietlenie zgłoszonych artykułów
     * Są zgłoszone
     * Nie są usuniete
     * Posortowane od najnowszego
     */

    public function findAllArticlesReported()
    {
        return $this -> createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.reported = TRUE')
            ->andWhere('a.isDeleted != TRUE')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /*
     * src/Controller/Admin/AdminController
     * Wszystkie opublikowane
     * Nie są zgłoszone
     * Nie są usunięte
     * Posortowane od najnowszego
     */

    public function findAllPublishedAdmin()
    {
        return $this -> createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.reported != TRUE')
            ->andWhere('a.isDeleted != TRUE')
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // ===== ARTYKUŁ =====
    /*
     * src/Controller/Article/ArticleNewsListController
     * Artykuły dodane w ciągu ostatnich 3 dni
     * Nie są zgłoszone
     * Nie są usunięte
     * Posortowane od najnowszego
     * Wyświetlanie listy najnowszych artykułów
     */

    public function findAllPublishedLastThreeDays()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt > CURRENT_DATE() - 3')
            ->andWhere('a.reported != TRUE')
            ->andWhere('a.isDeleted != TRUE')
            ->orderBy('a.publishedAt', 'DESC');
    }

    /*
     * src/Controller/Article/ArticleListController
     * Wszystkie opublikowane artykuły
     * Nie są zgłoszone
     * Nie są usuniete
     * Posortowane od najnowszego
     */

    public function findAllPublishedByNewest()
    {
        return $this -> createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.reported != TRUE')
            ->andWhere('a.isDeleted != TRUE')
            ->orderBy('a.publishedAt', 'DESC');
    }

    /*
     * /src/Controller/Article/ArticleSearchController
     * Wyszukiwanie artykułu po TYTULE
     * Nie jest zgłoszony
     * Nie jest usunięty
     * Posortowane od najnowszego
     */

    public function findAllPublishedByTitle(?string $term)
    {
        $qb = $this -> createQueryBuilder('a');

        if ($term) {
            $qb -> andWhere('a.title LIKE :term')
                -> andWhere('a.reported != TRUE')
                -> andWhere('a.isDeleted != TRUE')
                -> setParameter('term', '%' . $term . '%');
        }

        return $qb
            ->orderBy('a.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


    // ===== ACCOUNT =====
    /*
     * src/Controller/Account/AccountController
     * Artykuł dodany przez zalogowanego użytkownika
     * Nie jest zgłoszony
     * Nie jest usuniety
     */

    public function findAllPublishedByUser($term)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.author =' .$term)
            ->andWhere('a.reported != TRUE')
            ->andWhere('a.isDeleted != TRUE')
            ->getQuery()
            ->getResult()
            ;
    }

    // ===== BEZ USAGE =====
    /*
     * Wyszukanie artykuły po ID
     */
    /*public function findByID($term)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id =' .$term)
            ->getQuery()
            ->getResult()
            ;
    }*/
}
