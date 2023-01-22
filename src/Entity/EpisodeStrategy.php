<?php

namespace App\Entity;

use App\Repository\EpisodeStrategyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpisodeStrategyRepository::class)]
class EpisodeStrategy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?int $length = null;

    #[ORM\Column]
    private ?bool $isRandom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ExpressionScript = null;

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

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function isIsRandom(): ?bool
    {
        return $this->isRandom;
    }

    public function setIsRandom(bool $isRandom): self
    {
        $this->isRandom = $isRandom;

        return $this;
    }

    public function getExpressionScript(): ?string
    {
        return $this->ExpressionScript;
    }

    public function setExpressionScript(?string $ExpressionScript): self
    {
        $this->ExpressionScript = $ExpressionScript;

        return $this;
    }
}
