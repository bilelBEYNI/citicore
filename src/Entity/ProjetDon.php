<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Association;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ProjetDonRepository;

#[ORM\Entity(repositoryClass: ProjetDonRepository::class)]
#[ORM\Table(name: 'projet_don')]
class ProjetDon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_Projet_Don = null;

    #[ORM\ManyToOne(targetEntity: Association::class)]
    #[ORM\JoinColumn(name: "id_association", referencedColumnName: "id_association")]
    private ?Association $association = null;  // Relationship field

    public function getId_Projet_Don(): ?int
    {
        return $this->id_Projet_Don;
    }

    public function setId_Projet_Don(int $id_Projet_Don): self
    {
        $this->id_Projet_Don = $id_Projet_Don;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]

    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(name: 'montantRecu',type: 'decimal', nullable: false)]
    #[Assert\NotBlank(message: 'Le montant reçu est obligatoire.')]
    #[Assert\Type(type: 'numeric', message: 'Le montant reçu doit être un nombre.')]
    private ?float $montantRecu = 0;

    public function getMontantRecu(): ?float
    {
        return $this->montantRecu;
    }

    public function setMontantRecu(float $montantRecu): self
    {
        $this->montantRecu = $montantRecu;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    #[Assert\NotBlank(message: 'L\'objectif est obligatoire.')]
    #[Assert\Type(type: 'numeric', message: 'L\'objectif doit être un nombre.')]
    private ?float $objectif = null;

    public function getObjectif(): ?float
    {
        return $this->objectif;
    }

    public function setObjectif(float $objectif): self
    {
        $this->objectif = $objectif;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotBlank(message: 'La date de début est obligatoire.')]
    #[Assert\Type(type: 'DateTimeInterface', message: 'La date de début doit être une date valide.')]
    private ?\DateTimeInterface $date_debut = null;

    public function getDate_debut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDate_debut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire.')]
    #[Assert\Type(type: 'DateTimeInterface', message: 'La date de fin doit être une date valide.')]
    private ?\DateTimeInterface $date_fin = null;

    public function getDate_fin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDate_fin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $id_association = null;

    public function getId_association(): ?int
    {
        return $this->id_association;
    }

    public function setId_association(?int $id_association): self
    {
        $this->id_association = $id_association;
        return $this;
    }

    public function getIdProjetDon(): ?int
    {
        return $this->id_Projet_Don;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getIdAssociation(): ?int
    {
        return $this->id_association;
    }

    public function setIdAssociation(?int $id_association): static
    {
        $this->id_association = $id_association;

        return $this;
    }
    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): self
    {
        $this->association = $association;
        return $this;
    }
    




























    

}
