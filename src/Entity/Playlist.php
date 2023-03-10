<?php

namespace App\Entity;

use App\Repository\EpisodeStrategyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: EpisodeStrategyRepository::class)]
class Playlist
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['GET'])]
    private ?string $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['GET'])]
    private ?string $title = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Groups(['GET'])]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['GET'])]
    private ?string $link = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $disabledAt = null;
    #[ORM\OneToMany(mappedBy: 'playlist', targetEntity: PlaylistConfig::class, cascade: ['persist', 'remove'])]
    #[Groups(['GET'])]
    private Collection $playlistConfigs;

    #[ORM\OneToMany(mappedBy: 'playlist', targetEntity: PlaylistEpisode::class, orphanRemoval: true)]
    private Collection $playlistEpisodes;

    public function __construct()
    {
        $this->playlistConfigs = new ArrayCollection();
        $this->playlistEpisodes = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(): ?string
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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
     * @return Collection<int, PlaylistConfig>
     */
    public function getPlaylistConfigs(): Collection
    {
        return $this->playlistConfigs;
    }

    public function setPlaylistConfigs(array $playlistsConfigs): self
    {
        $this->playlistConfigs = new ArrayCollection($playlistsConfigs);

        return $this;
    }

    public function addPlaylistConfig(PlaylistConfig $playlistConfig): self
    {
        if (!$this->playlistConfigs->contains($playlistConfig)) {
            $this->playlistConfigs->add($playlistConfig);
            $playlistConfig->setPlaylist($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PlaylistEpisode>
     */
    public function getPlaylistEpisodes(): Collection
    {
        return $this->playlistEpisodes;
    }

    public function addPlaylistEpisode(PlaylistEpisode $playlistEpisode): self
    {
        if (!$this->playlistEpisodes->contains($playlistEpisode)) {
            $this->playlistEpisodes->add($playlistEpisode);
            $playlistEpisode->setPlaylist($this);
        }

        return $this;
    }

    public function removePlaylistEpisode(PlaylistEpisode $playlistEpisode): self
    {
        if ($this->playlistEpisodes->removeElement($playlistEpisode)) {
            // set the owning side to null (unless already changed)
            if ($playlistEpisode->getPlaylist() === $this) {
                $playlistEpisode->setPlaylist(null);
            }
        }

        return $this;
    }
}
