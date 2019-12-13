<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /* Dodaje pola created_at i updated_at do encji */
    use TimestampableEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authorFirstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authorLastName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;


    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getAuthorFirstName(): ?string
    {
        return $this->authorFirstName;
    }

    public function setAuthorFirstName(string $authorFirstName): self
    {
        $this->authorFirstName = $authorFirstName;

        return $this;
    }

    public function getAuthorLastName(): ?string
    {
        return $this->authorLastName;
    }

    public function setAuthorLastName(string $authorLastName): self
    {
        $this->authorLastName = $authorLastName;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
