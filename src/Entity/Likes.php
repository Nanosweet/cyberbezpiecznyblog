<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LikesRepository")
 */
class Likes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /*
    /**
     * @ORM\Column(type="integer")

    private $count;
    */

    /**
     * @ORM\Column(type="integer")
     */
    private $userid;

    /**
     * @ORM\Column(type="integer")
     */
    private $postid;
    /*
     *


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="likesCount")
     * @ORM\JoinColumn(nullable=false)


    private $articleID;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="likes")
     * @ORM\JoinColumn(nullable=false)

    private $userID;
    */
    public function getId(): ?int
    {
        return $this->id;
    }

    /*
    public function getCount(): ?int
    {
        return $this->count;
    }


    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }
    */

    /*
    public function getArticleID(): ?Article
    {
        return $this->articleID;
    }


    public function setArticleID(?Article $articleID): self
    {
        $this->articleID = $articleID;

        return $this;
    }

    public function getUserID(): ?User
    {
        return $this->userID;
    }

    public function setUserID(?User $userID): self
    {
        $this->userID = $userID;

        return $this;
    }
    */
    public function incrementLikesCount(): self
    {
        $this->count = $this->count + 1;

        return $this;
    }

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(int $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getPostid(): ?int
    {
        return $this->postid;
    }

    public function setPostid(int $postid): self
    {
        $this->postid = $postid;

        return $this;
    }
}
