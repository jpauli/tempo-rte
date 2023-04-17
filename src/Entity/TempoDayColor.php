<?php

namespace App\Entity;

use App\Repository\TempoDayColorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Model\TempoColors;

#[ORM\Entity(repositoryClass: TempoDayColorRepository::class)]
class TempoDayColor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $day = null;

    #[ORM\Column(length: 1)]
    private ?int $color = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?\DateTimeInterface
    {
        return $this->day;
    }

    public function isToday(): bool
    {
        return $this->getDay()->format('d') == (new \DateTime())->format('d');
    }

    public function setDay(\DateTimeInterface $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getColor(): int
    {
        return $this->color;
    }

    public function setColor(TempoColors $color): self
    {
        $this->color = $color->value;

        return $this;
    }

    public function getColorName(): string
    {
        return strtolower(TempoColors::from($this->color)->name);
    }
}