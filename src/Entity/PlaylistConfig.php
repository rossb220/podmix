<?php

namespace App\Entity;

use App\Repository\PlaylistConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PlaylistConfigRepository::class)]
#[ORM\UniqueConstraint(
    name: 'playlist_position_unique_idx',
    columns: ['playlist_id', 'position']
)]
class PlaylistConfig
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['GET'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'playlistConfigs')]
    private ?Playlist $playlist = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['GET'])]
    private ?EpisodeStrategy $episodeStrategy = null;

    #[ORM\Column()]
    #[Groups(['GET'])]
    private ?int $position = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(): ?string
    {
        return $this->id;
    }

    public function getPlaylist(): ?Playlist
    {
        return $this->playlist;
    }

    public function setPlaylist(?Playlist $playlist): self
    {
        $this->playlist = $playlist;

        return $this;
    }

    public function getEpisodeStrategy(): ?EpisodeStrategy
    {
        return $this->episodeStrategy;
    }

    public function setEpisodeStrategy(?EpisodeStrategy $episodeStrategy): self
    {
        $this->episodeStrategy = $episodeStrategy;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
