<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\ReponseRepository;
use App\Entity\Reclamation;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
#[ORM\Table(name: 'reponse')]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID_Reponse', type: 'integer')]
    private ?int $ID_Reponse = null;

    public function getID_Reponse(): ?int
    {
        return $this->ID_Reponse;
    }

    public function setID_Reponse(int $ID_Reponse): self
    {
        $this->ID_Reponse = $ID_Reponse;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(name: 'ID_Reclamation', referencedColumnName: 'ID_Reclamation')]
    #[Assert\NotNull(message: "La réclamation associée est obligatoire.")]
    private ?Reclamation $reclamation = null;

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;
        return $this;
    }

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La contenu ne doit pas être vide.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La contenudoit contenir au moins {{ limit }} caractères.'
    )]
    private ?string $Contenu = null;

    public function getContenu(): ?string
    {
        return $this->Contenu;
    }

    public function setContenu(string $Contenu): self
    {
        $this->Contenu = $Contenu;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotNull(message: 'La date de réponse est requise.')]
    #[Assert\Type(type: "DateTimeInterface", message: 'La date de réponse doit être une date valide.')]
    #[Assert\LessThanOrEqual(
        'now',
        message: 'La date de réponse ne peut pas être dans le futur.'
    )]
    private ?\DateTimeInterface $DateReponse = null;

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->DateReponse;
    }

    public function setDateReponse(?\DateTimeInterface $DateReponse): self
    {
        $this->DateReponse = $DateReponse;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['Traitée', 'En Cours', 'Rejetée'],
        message: "Le statut doit être 'Traitée', 'En Cours' ou 'Rejeteé'."
    )]
    private ?string $Statut = null;

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(?string $Statut): self
    {
        $this->Statut = $Statut;
        return $this;
    }

}
