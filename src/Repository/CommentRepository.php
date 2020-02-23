<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    // ===== ADMIN =====
    /*
     * src/Controller/Admin/AdminController
     * Wyświetlenie wszystkich
     * Nie sa zgłoszone
     * Nie sa usuniete
     */
    public function findAllPublishedNonDeletedNonReported()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.createdAt IS NOT NULL')
            ->andWhere('a.isDeleted != TRUE')
            ->andWhere('a.isReported != TRUE')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /*
     * src/Controller/Account/AccountController
     * Wybranie komentarzy dodanych przez zalogowanego użytkwonika
     * Parametr id usera
     * Posortowane od najnowszego
     * Wyswietla wszystkie nawet usuniete i zgłoszone
     */
    public function findAllCommentedByUser($term)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.author =' .$term)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }


    /*
     * src/Controller/Admin/AdminController
     * Wyświetlenie zgłoszonych komentarzy
     */

    public function findAllReported()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.createdAt IS NOT NULL')
            ->andWhere('a.isDeleted != TRUE')
            ->andWhere('a.isReported = TRUE')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    /*
     * src/Controller/Admin/AdminController
     */
    public function findAllDeleted()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.createdAt IS NOT NULL')
            ->andWhere('a.isDeleted = TRUE')
            ->andWhere('a.isReported != TRUE')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    /*
     * Znajdź wszystkie komentarze, które są do tego artykułu
     */
    public function findAllByArticleID($term)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.article =' .$term)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // ===== ARTYKUŁ =====
    /*
     * src/Controller/Article/ArticleController
     * Funkcja wybierajaca z bazy wszystkie komentarze
     * Nie sa zgłoszone
     * Nie sa usunięte
     * Posortowane od najnowszego
     */

    public function findAllPublishedByNewest()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.createdAt IS NOT NULL')
            ->andWhere('a.isDeleted != TRUE')
            ->andWhere('a.isReported != TRUE')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // ===== ACCOUNT =====
    /*
     * src/Controller/Account/AccountController
     * Wybranie komentarzy dodanych przez zalogowanego użytkwonika
     * Parametr id usera
     * Nie sa zgłoszone
     * Nie sa usuniete
     * Posortowane od najnowszego
     */
    public function findAllCommentedByUserNonDeletedNonReported($userID)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.author =' .$userID)
            ->andWhere('a.isReported != TRUE')
            ->andWhere('a.isDeleted != TRUE')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // ===== BEZ USAGE =====

}
