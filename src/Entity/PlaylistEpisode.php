<?php

namespace App\Entity;

use App\Repository\PlaylistEpisodeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;

#[ORM\Entity(repositoryClass: PlaylistEpisodeRepository::class)]
class PlaylistEpisode
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?string $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Episode $episode = null;

    #[ORM\OneToOne(inversedBy: 'playlistEpisode', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Playlist $Playlist = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $publishedAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(): ?string
    {
        return $this->id;
    }

    public function getEpisode(): ?Episode
    {
        return $this->episode;
    }

    public function setEpisode(Episode $episode): self
    {
        $this->episode = $episode;

        return $this;
    }

    public function getPlaylist(): ?Playlist
    {
        return $this->Playlist;
    }

    public function setPlaylist(Playlist $Playlist): self
    {
        $this->Playlist = $Playlist;

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
}
