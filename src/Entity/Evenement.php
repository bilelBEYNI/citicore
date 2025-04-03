<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\EvenementRepository;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[ORM\Table(name: 'evenement')]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_evenement = null;

    public function getId_evenement(): ?int
    {
        return $this->id_evenement;
    }

    public function setId_evenement(int $id_evenement): self
    {
        $this->id_evenement = $id_evenement;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom_evenement = null;

    public function getNom_evenement(): ?string
    {
        return $this->nom_evenement;
    }

    public function setNom_evenement(string $nom_evenement): self
    {
        $this->nom_evenement = $nom_evenement;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $date_evenement = null;

    public function getDate_evenement(): ?string
    {
        return $this->date_evenement;
    }

    public function setDate_evenement(string $date_evenement): self
    {
        $this->date_evenement = $date_evenement;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $lieu_evenement = null;

    public function getLieu_evenement(): ?string
    {
        return $this->lieu_evenement;
    }

    public function setLieu_evenement(string $lieu_evenement): self
    {
        $this->lieu_evenement = $lieu_evenement;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'categorie_id')]
    private ?Categorie $categorie = null;

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(name: 'organisateur_id', referencedColumnName: 'Cin')]
    private ?Utilisateur $utilisateur = null;

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getIdEvenement(): ?int
    {
        return $this->id_evenement;
    }

    public function getNomEvenement(): ?string
    {
        return $this->nom_evenement;
    }

    public function setNomEvenement(string $nom_evenement): static
    {
        $this->nom_evenement = $nom_evenement;

        return $this;
    }

    public function getDateEvenement(): ?string
    {
        return $this->date_evenement;
    }

    public function setDateEvenement(string $date_evenement): static
    {
        $this->date_evenement = $date_evenement;

        return $this;
    }

    public function getLieuEvenement(): ?string
    {
        return $this->lieu_evenement;
    }

    public function setLieuEvenement(string $lieu_evenement): static
    {
        $this->lieu_evenement = $lieu_evenement;

        return $this;
    }

}
