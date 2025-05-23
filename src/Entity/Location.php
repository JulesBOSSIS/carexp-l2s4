<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('location')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups('location')]
    private ?\DateTimeInterface $dateDebutLocation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups('location')]
    private ?\DateTimeInterface $dateFinLocation = null;

    #[ORM\Column]
    #[Groups('location')]
    private ?float $prixTotal = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $locataire = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('location')]
    private ?Agence $agenceLoueuse = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Voiture $voitureLoue = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutLocation(): ?\DateTimeInterface
    {
        return $this->dateDebutLocation;
    }

    public function setDateDebutLocation(\DateTimeInterface $dateDebutLocation): static
    {
        $this->dateDebutLocation = $dateDebutLocation;

        return $this;
    }

    public function getDateFinLocation(): ?\DateTimeInterface
    {
        return $this->dateFinLocation;
    }

    public function setDateFinLocation(\DateTimeInterface $dateFinLocation): static
    {
        $this->dateFinLocation = $dateFinLocation;

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getLocataire(): ?User
    {
        return $this->locataire;
    }

    public function setLocataire(?User $locataire): static
    {
        $this->locataire = $locataire;

        return $this;
    }

    public function getAgenceLoueuse(): ?Agence
    {
        return $this->agenceLoueuse;
    }

    public function setAgenceLoueuse(?Agence $agenceLoueuse): static
    {
        $this->agenceLoueuse = $agenceLoueuse;

        return $this;
    }

    public function getVoitureLoue(): ?Voiture
    {
        return $this->voitureLoue;
    }

    public function setVoitureLoue(?Voiture $voitureLoue): static
    {
        $this->voitureLoue = $voitureLoue;

        return $this;
    }
}
