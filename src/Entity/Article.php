<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\Timestampable;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @UniqueEntity(fields={"title"})
 */
class Article
{
    use TimestampableEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /*
     * typ danych to string
     * dlugosc to 255
     * kolumna jest unikalna
     * wymagany ciąg znaków
     *  regex - Wyrażenie regularne - wzorzec opisujący łańcuch symboli:
     * ą-ż - pojedynczy znak z zakresu od ą do ż
     * Ą-Ż - pojedynczy znak z zakresu od Ą do Ż
     * /S - pasuje do dowolnego znaku spacji
     * /s - pasuje do każdego znaku białych znaków */

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Regex(
     *     pattern="/^[ą-żĄ-Ż\S\s]+$/",
     *     message="Ta wartość jest nieprawidłowa"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="article")
     */
    private $comments;

    /**
     * @ORM\Column(type="integer")
     */
    private $likes = 0;

    /*
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Likes", mappedBy="articleID")

    private $likesCount = 0;

    */
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $reported;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likesCount = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

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

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
    public function getNonDeletedComments(): Collection
    {
        $comments = [];

        foreach ($this->getComments() as $comment) {
            if (!$comment->getIsDeleted()) {
                $comments[] = $comment;
            }
        }

        return new ArrayCollection($comments);
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function incrementLikes(): self
    {
        $this->likes = $this->likes + 1;

        return $this;
    }
    public function decrementLikes(): self
    {
        if ($this->likes > 0)
            $this->likes = $this->likes - 1;
        else
            $this->likes = $this->likes = 0;

        return $this;
    }

    /*
    /**
     * @return Collection|Likes[]

    public function getLikesCount(): Collection
    {
        return $this->likesCount;
    }

    public function addLikesCount(Likes $likesCount): self
    {
        if (!$this->likesCount->contains($likesCount)) {
            $this->likesCount[] = $likesCount;
            $likesCount->setArticleID($this);
        }

        return $this;
    }

    public function removeLikesCount(Likes $likesCount): self
    {
        if ($this->likesCount->contains($likesCount)) {
            $this->likesCount->removeElement($likesCount);
            // set the owning side to null (unless already changed)
            if ($likesCount->getArticleID() === $this) {
                $likesCount->setArticleID(null);
            }
        }

        return $this;
    }

    public function incrementLikesCount(): self
    {
        $this->likesCount = $this->likesCount + 1;

        return $this;
    }
    */

    public function getReported(): ?bool
    {
        return $this->reported;
    }

    public function setReported(?bool $reported): self
    {
        $this->reported = $reported;

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
