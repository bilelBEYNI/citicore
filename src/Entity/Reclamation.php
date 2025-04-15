<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\ReclamationRepository;
use App\Entity\Reponse;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: 'reclamation')]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID_Reclamation', type: 'integer')]
    private ?int $ID_Reclamation = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le sujet ne doit pas être vide.')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Le sujet doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le sujet ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $Sujet = null;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank(message: 'La description ne doit pas être vide.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.'
    )]
    private ?string $Description = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $Date_Creation = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $Date_Resolution = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Choice(
        choices: ['Marketplace', 'Demande', 'Evenement', 'Projetdon'],
        message: 'Le type de réclamation doit être : Marketplace, Demande, Evenement ou Projetdon.'
    )]
    private ?string $Type_Reclamation = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotNull(message: 'Le numéro CIN de l’utilisateur est requis.')]
    #[Assert\Positive(message: 'Le CIN doit être un nombre positif.')]
    #[Assert\Length(
        min: 8,
        max: 8,
        exactMessage: 'Le CIN doit contenir exactement {{ limit }} chiffres.'
    )]
    private ?int $Cin_Utilisateur = null;

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'reclamation')]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getID_Reclamation(): ?int
    {
        return $this->ID_Reclamation;
    }

    public function setID_Reclamation(int $ID_Reclamation): self
    {
        $this->ID_Reclamation = $ID_Reclamation;
        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->Sujet;
    }

    public function setSujet(string $Sujet): self
    {
        $this->Sujet = $Sujet;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;
        return $this;
    }

    public function getDate_Creation(): ?\DateTimeInterface
    {
        return $this->Date_Creation;
    }

    public function setDate_Creation(?\DateTimeInterface $Date_Creation): self
    {
        $this->Date_Creation = $Date_Creation;
        return $this;
    }

    public function getDate_Resolution(): ?\DateTimeInterface
    {
        return $this->Date_Resolution;
    }

    public function setDate_Resolution(?\DateTimeInterface $Date_Resolution): self
    {
        $this->Date_Resolution = $Date_Resolution;
        return $this;
    }

    public function getType_Reclamation(): ?string
    {
        return $this->Type_Reclamation;
    }

    public function setType_Reclamation(?string $Type_Reclamation): self
    {
        $this->Type_Reclamation = $Type_Reclamation;
        return $this;
    }

    public function getCin_Utilisateur(): ?int
    {
        return $this->Cin_Utilisateur;
    }

    public function setCin_Utilisateur(int $Cin_Utilisateur): self
    {
        $this->Cin_Utilisateur = $Cin_Utilisateur;
        return $this;
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

    // Alias getter/setter (camelCase)
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
