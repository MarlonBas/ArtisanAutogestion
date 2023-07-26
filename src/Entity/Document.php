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
    private String $numero;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private String $type;

    #[ORM\ManyToOne(inversedBy: 'Documents')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'document', targetEntity: Designation::class)]
    private Collection $designations;

    public function __construct()
    {
        $this->designations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?String
    {
        return $this->numero;
    }

    public function setNumero(String $numero): static
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
            $designation->setDocument($this);
        }

        return $this;
    }

    public function removeDesignation(Designation $designation): static
    {
        if ($this->designations->removeElement($designation)) {
            // set the owning side to null (unless already changed)
            if ($designation->getDocument() === $this) {
                $designation->setDocument(null);
            }
        }

        return $this;
    }

    public function cloneDocument(): Document
    {
        $clonedDocument = new Document();
        $clonedDocument->setDate($this->getDate());
        $clonedDocument->setNumero($this->getNumero());
        $clonedDocument->setType($this->getType());
        $clonedDocument->setClient($this->getClient());
        $clonedDocument->setUser($this->getUser());

        foreach ($this->getDesignations() as $designation) {
            $clonedDesignation = new Designation();
            $clonedDesignation->setDescription($designation->getDescription());
            $clonedDesignation->setUnite($designation->getUnite());
            $clonedDesignation->setPrixUnitaire($designation->getPrixUnitaire());
            $clonedDesignation->setQuantite($designation->getQuantite());
            $clonedDesignation->setPrixHorsTax($designation->getPrixHorsTax());
            $clonedDesignation->setTva($designation->getTva());

            $clonedDocument->addDesignation($clonedDesignation);
        }

        return $clonedDocument;
    }

}
