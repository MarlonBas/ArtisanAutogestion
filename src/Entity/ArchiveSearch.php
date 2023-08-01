<?php

namespace App\Entity;

use App\Repository\ArchiveSearchRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArchiveSearchRepository::class)]
class ArchiveSearch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $searchString = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearchString(): ?string
    {
        return $this->searchString;
    }

    public function setSearchString(?string $searchString): static
    {
        $this->searchString = $searchString;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(?\DateTimeInterface $date): static
    {
        $this->dateStart = $date;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $date): static
    {
        $this->dateEnd = $date;

        return $this;
    }
}
