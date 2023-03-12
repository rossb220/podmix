<?php

namespace App\Entity;

use App\Repository\PodcastRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PodcastRepository::class)]
class Podcast
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['GET', 'EpisodeStrategy'])]
    private ?string $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['GET'])]
    private ?string $title = null;

    #[ORM\Column(length: 1000, unique: true)]
    #[Groups(['GET'])]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['GET'])]
    private ?\DateTimeImmutable $disabledAt = null;

    #[ORM\OneToMany(mappedBy: 'podcast', targetEntity: Episode::class)]
    #[Groups(['GET'])]
    private Collection $episodes;

    #[ORM\Column(length: 255)]
    #[Groups(['GET'])]
    private ?string $customTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['GET'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['GET'])]
    private ?\DateTimeImmutable $pubDate = null;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getDisabledAt(): ?\DateTimeImmutable
    {
        return $this->disabledAt;
    }

    public function setDisabledAt(?\DateTimeImmutable $disabledAt): self
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    /**
     * @return Collection<int, Episode>
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes->add($episode);
            $episode->setPodcast($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->removeElement($episode)) {
            // set the owning side to null (unless already changed)
            if ($episode->getPodcast() === $this) {
                $episode->setPodcast(null);
            }
        }

        return $this;
    }

    public function getCustomTitle(): ?string
    {
        return $this->customTitle;
    }

    public function setCustomTitle(string $customTitle): self
    {
        $this->customTitle = $customTitle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPubDate(): ?\DateTimeImmutable
    {
        return $this->pubDate;
    }

    public function setPubDate(?\DateTimeImmutable $pubDate): self
    {
        $this->pubDate = $pubDate;

        return $this;
    }

    public function __toString() : string
    {
        return $this->customTitle;
    }
}
