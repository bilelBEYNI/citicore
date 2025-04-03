<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReclamationRepository;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: 'reclamation')]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID_Reclamation', type: 'integer')]
    private ?int $ID_Reclamation = null;

    public function getID_Reclamation(): ?int
    {
        return $this->ID_Reclamation;
    }

    public function setID_Reclamation(int $ID_Reclamation): self
    {
        $this->ID_Reclamation = $ID_Reclamation;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Sujet = null;

    public function getSujet(): ?string
    {
        return $this->Sujet;
    }

    public function setSujet(string $Sujet): self
    {
        $this->Sujet = $Sujet;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $Description = null;

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $Date_Creation = null;

    public function getDate_Creation(): ?\DateTimeInterface
    {
        return $this->Date_Creation;
    }

    public function setDate_Creation(?\DateTimeInterface $Date_Creation): self
    {
        $this->Date_Creation = $Date_Creation;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $Date_Resolution = null;

    public function getDate_Resolution(): ?\DateTimeInterface
    {
        return $this->Date_Resolution;
    }

    public function setDate_Resolution(?\DateTimeInterface $Date_Resolution): self
    {
        $this->Date_Resolution = $Date_Resolution;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Type_Reclamation = null;

    public function getType_Reclamation(): ?string
    {
        return $this->Type_Reclamation;
    }

    public function setType_Reclamation(?string $Type_Reclamation): self
    {
        $this->Type_Reclamation = $Type_Reclamation;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $Cin_Utilisateur = null;

    public function getCin_Utilisateur(): ?int
    {
        return $this->Cin_Utilisateur;
    }

    public function setCin_Utilisateur(int $Cin_Utilisateur): self
    {
        $this->Cin_Utilisateur = $Cin_Utilisateur;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'reclamation')]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        if (!$this->reponses instanceof Collection) {
            $this->reponses = new ArrayCollection();
        }
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->getReponses()->contains($reponse)) {
            $this->getReponses()->add($reponse);
        }
        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        $this->getReponses()->removeElement($reponse);
        return $this;
    }

    // Getter and setter methods for camelCase properties
    public function getIDReclamation(): ?int
    {
        return $this->ID_Reclamation;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->Date_Creation;
    }

    public function setDateCreation(?\DateTimeInterface $Date_Creation): static
    {
        $this->Date_Creation = $Date_Creation;

        return $this;
    }

    public function getDateResolution(): ?\DateTimeInterface
    {
        return $this->Date_Resolution;
    }

    public function setDateResolution(?\DateTimeInterface $Date_Resolution): static
    {
        $this->Date_Resolution = $Date_Resolution;

        return $this;
    }

    public function getTypeReclamation(): ?string
    {
        return $this->Type_Reclamation;
    }

    public function setTypeReclamation(?string $Type_Reclamation): static
    {
        $this->Type_Reclamation = $Type_Reclamation;

        return $this;
    }

    public function getCinUtilisateur(): ?int
    {
        return $this->Cin_Utilisateur;
    }

    public function setCinUtilisateur(int $Cin_Utilisateur): static
    {
        $this->Cin_Utilisateur = $Cin_Utilisateur;

        return $this;
    }
}
