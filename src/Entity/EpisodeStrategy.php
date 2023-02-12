<?php

namespace App\Entity;

use App\Repository\EpisodeStrategyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: EpisodeStrategyRepository::class)]
class EpisodeStrategy
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['EpisodeStrategy'])]
    private ?string $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['EpisodeStrategy'])]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['EpisodeStrategy'])]
    private ?int $length = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['EpisodeStrategy'])]
    private ?array $expression = null;

    #[ORM\ManyToMany(targetEntity: Podcast::class)]
    #[Groups(['EpisodeStrategy'])]
    private Collection $podcasts;

    public function __construct()
    {
        $this->podcasts = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getExpression(): ?array
    {
        return $this->expression;
    }

    public function setExpression(?array $expression): self
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @return Collection<int, Podcast>
     */
    public function getPodcasts(): Collection
    {
        return $this->podcasts;
    }

    public function addPodcast(Podcast $podcast): self
    {
        if (!$this->podcasts->contains($podcast)) {
            $this->podcasts->add($podcast);
        }

        return $this;
    }

    public function setPodcasts(Collection $podcasts): self
    {
        $this->podcasts = $podcasts;

        return $this;
    }

    public function removePodcast(Podcast $podcast): self
    {
        $this->podcasts->removeElement($podcast);

        return $this;
    }
}
