<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['venteApi'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['venteApi'])]
    private ?string $marque = null;

    #[ORM\Column(length: 255)]
    #[Groups(['venteApi'])]
    private ?string $modele = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['venteApi'])]
    private ?\DateTimeInterface $anneeMiseEnCirculation = null;

    #[ORM\Column]
    private ?float $kilometrage = null;

    #[ORM\Column]
    #[Groups(['venteApi'])]
    private ?float $prixVente = null;

    #[ORM\Column]
    private ?float $prixLocationParJour = null;

    #[ORM\Column]
    private ?bool $estEnVente = null;

    #[ORM\Column]
    private ?bool $estALouer = null;

    #[ORM\Column]
    private ?bool $clim = null;

    #[ORM\Column]
    private ?int $nombreDePlaces = null;

    #[ORM\Column]
    private ?float $emissons = null;

    #[ORM\ManyToOne(inversedBy: 'voitures')]
    private ?User $proprietaire = null;

    #[ORM\OneToOne(mappedBy: 'voiture', cascade: ['persist', 'remove'])]
    private ?Vente $vente = null;

    #[ORM\ManyToOne(inversedBy: 'voitures')]
    #[Groups(['venteApi'])]
    private ?Agence $agence = null;

    #[ORM\OneToMany(targetEntity: Location::class, mappedBy: 'voitureLoue')]
    private Collection $locations;

    #[ORM\Column(type: Types::ARRAY)]
    private array $images = [];

    #[ORM\ManyToOne(inversedBy: 'voitures')]
    private ?Categorie $categorie = null;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getanneeMiseEnCirculation(): ?\DateTime
    {
        return $this->anneeMiseEnCirculation;
    }

    public function setanneeMiseEnCirculation(\DateTime $anneeMiseEnCirculation): static
    {
        $this->anneeMiseEnCirculation = $anneeMiseEnCirculation;

        return $this;
    }

    public function getKilometrage(): ?float
    {
        return $this->kilometrage;
    }

    public function setKilometrage(float $kilometrage): static
    {
        $this->kilometrage = $kilometrage;

        return $this;
    }

    public function getPrixVente(): ?float
    {
        return $this->prixVente;
    }

    public function setPrixVente(float $prixVente): static
    {
        $this->prixVente = $prixVente;

        return $this;
    }

    public function getPrixLocationParJour(): ?float
    {
        return $this->prixLocationParJour;
    }

    public function setPrixLocationParJour(float $prixLocationParJour): static
    {
        $this->prixLocationParJour = $prixLocationParJour;

        return $this;
    }

    public function isEstEnVente(): ?bool
    {
        return $this->estEnVente;
    }

    public function setEstEnVente(bool $estEnVente): static
    {
        $this->estEnVente = $estEnVente;

        return $this;
    }

    public function isEstALouer(): ?bool
    {
        return $this->estALouer;
    }

    public function setEstALouer(bool $estALouer): static
    {
        $this->estALouer = $estALouer;

        return $this;
    }

    public function isClim(): ?bool
    {
        return $this->clim;
    }

    public function setClim(bool $clim): static
    {
        $this->clim = $clim;

        return $this;
    }

    public function getNombreDePlaces(): ?int
    {
        return $this->nombreDePlaces;
    }

    public function setNombreDePlaces(int $nombreDePlaces): static
    {
        $this->nombreDePlaces = $nombreDePlaces;

        return $this;
    }

    public function getEmissons(): ?float
    {
        return $this->emissons;
    }

    public function setEmissons(float $emissons): static
    {
        $this->emissons = $emissons;

        return $this;
    }

    public function getProprietaire(): ?User
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?User $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    public function getVente(): ?Vente
    {
        return $this->vente;
    }

    public function setVente(Vente $vente): static
    {
        // set the owning side of the relation if necessary
        if ($vente->getVoiture() !== $this) {
            $vente->setVoiture($this);
        }

        $this->vente = $vente;

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): static
    {
        $this->agence = $agence;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): static
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setVoitureLoue($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): static
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getVoitureLoue() === $this) {
                $location->setVoitureLoue(null);
            }
        }

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function addImage(string $image): self
    {
        $this->images[] = $image;

        return $this;
    }

    public function removeImage(string $image): self
    {
        if (false !== $key = array_search($image, $this->images, true)) {
            unset($this->images[$key]);
            $this->images = array_values($this->images);
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }
}
