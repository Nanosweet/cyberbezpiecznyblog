<?php

namespace App\Repository;

use App\Entity\Likes;
use App\Entity\User;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Likes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Likes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Likes[]    findAll()
 * @method Likes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    public function findAllLiked()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.count IS NOT NULL')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findAllLikedByUserID($userID)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.userid =' .$userID)
            ->getQuery()
            ->getResult()
            ;
    }
    public function findLikedPostByUser(Article $article, User $user): ?Likes
    {
        try {
            return $this->createQueryBuilder('a')
                ->andWhere('a.postid = :article_id')
                ->andWhere('a.userid = :user_id')
                ->setParameter('article_id', $article->getId())
                ->setParameter('user_id', $user->getId())
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findAllByArticleUserID($term1, $term2)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.postid =' .$term1)
            ->andWhere('a.userid =' .$term2)
            ->getQuery()
            ->getResult()
            ;
    }


}
