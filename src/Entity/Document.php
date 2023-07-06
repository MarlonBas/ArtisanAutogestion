<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private String $type;

    #[ORM\ManyToMany(targetEntity: Designation::class, mappedBy: 'document')]
    private Collection $designations;

    #[ORM\ManyToOne(inversedBy: 'Documents')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->designations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): String
    {
        return $this->type;
    }

    public function setType(String $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Designation>
     */
    public function getDesignations(): Collection
    {
        return $this->designations;
    }

    public function addDesignation(Designation $designation): static
    {
        if (!$this->designations->contains($designation)) {
            $this->designations->add($designation);
            $designation->addDocument($this);
        }

        return $this;
    }

    public function removeDesignation(Designation $designation): static
    {
        if ($this->designations->removeElement($designation)) {
            $designation->removeDocument($this);
        }

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
