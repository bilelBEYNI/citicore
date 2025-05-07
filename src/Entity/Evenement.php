<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id_evenement = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'evenements')]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'categorie_id', nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom_evenement;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $date_evenement = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $lieu_evenement = null;

    #[ORM\Column(type: "integer")]
    private int $organisateur_id;

    // Getters and Setters

    public function getId_Evenement(): ?int
    {
        return $this->id_evenement;
    }

    public function setId_Evenement(?int $id_evenement): self
    {
        $this->id_evenement = $id_evenement;
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getNom_Evenement(): string
    {
        return $this->nom_evenement;
    }

    public function setNom_Evenement(string $nom_evenement): self
    {
        $this->nom_evenement = $nom_evenement;
        return $this;
    }

    public function getDate_Evenement(): ?\DateTimeInterface
    {
        return $this->date_evenement;
    }

    public function setDate_Evenement(?\DateTimeInterface $date_evenement): self
    {
        $this->date_evenement = $date_evenement;
        return $this;
    }

    public function getLieu_Evenement(): ?string
    {
        return $this->lieu_evenement;
    }

    public function setLieu_Evenement(?string $lieu_evenement): self
    {
        $this->lieu_evenement = $lieu_evenement;
        return $this;
    }
    public function getNomEvenement(): string
{
    return $this->nom_evenement;
}

public function setNomEvenement(string $nom_evenement): self
{
    $this->nom_evenement = $nom_evenement;
    return $this;
}
public function getDateEvenement(): ?\DateTimeInterface
    {
        return $this->date_evenement;
    }

    public function setDateEvenement(?\DateTimeInterface $date_evenement): self
    {
        $this->date_evenement = $date_evenement;
        return $this;
    }

    public function getLieuEvenement(): ?string
    {
        return $this->lieu_evenement;
    }

    public function setLieuEvenement(?string $lieu_evenement): self
    {
        $this->lieu_evenement = $lieu_evenement;
        return $this;
    }
    public function getOrganisateurId(): int
{
    return $this->organisateur_id;
}

public function setOrganisateurId(int $organisateur_id): self
{
    $this->organisateur_id = $organisateur_id;
    return $this;
}

}