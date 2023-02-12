<?php

namespace App\Entity;

use App\Repository\EpisodeStrategyRepository;
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
    #[Groups(['GET'])]
    private ?string $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['GET'])]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['GET'])]
    private ?int $length = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['GET'])]
    private ?string $expression = null;

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

    public function getExpression(): ?string
    {
        return $this->ExpressionScript;
    }

    public function setExpression(?string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }
}
