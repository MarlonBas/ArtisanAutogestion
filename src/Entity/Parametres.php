<?php

namespace App\Entity;

use App\Repository\ParametresRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParametresRepository::class)]
class Parametres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $modeMicro = null;

    #[ORM\Column(nullable: true)]
    private ?bool $afficherSommes = null;

    #[ORM\Column(nullable: true)]
    private ?bool $afficherCalendrier = null;

    #[ORM\Column(nullable: true)]
    private ?bool $afficherGraphique = null;

    #[ORM\Column(nullable: true)]
    private ?bool $afficherImpots = null;

    #[ORM\OneToOne(inversedBy: 'parametres', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isModeMicro(): ?bool
    {
        return $this->modeMicro;
    }

    public function setModeMicro(?bool $modeMicro): static
    {
        $this->modeMicro = $modeMicro;

        return $this;
    }

    public function isAfficherSommes(): ?bool
    {
        return $this->afficherSommes;
    }

    public function setAfficherSommes(?bool $afficherSommes): static
    {
        $this->afficherSommes = $afficherSommes;

        return $this;
    }

    public function isAfficherCalendrier(): ?bool
    {
        return $this->afficherCalendrier;
    }

    public function setAfficherCalendrier(?bool $afficherCalendrier): static
    {
        $this->afficherCalendrier = $afficherCalendrier;

        return $this;
    }

    public function isAfficherGraphique(): ?bool
    {
        return $this->afficherGraphique;
    }

    public function setAfficherGraphique(bool $afficherGraphique): static
    {
        $this->afficherGraphique = $afficherGraphique;

        return $this;
    }

    public function isAfficherImpots(): ?bool
    {
        return $this->afficherImpots;
    }

    public function setAfficherImpots(?bool $afficherImpots): static
    {
        $this->afficherImpots = $afficherImpots;

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
