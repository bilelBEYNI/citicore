<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReponseRepository;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
#[ORM\Table(name: 'reponse')]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
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

    #[ORM\Column(type: 'text', nullable: false)]
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
    private ?\DateTimeInterface $Date_Reponse = null;

    public function getDate_Reponse(): ?\DateTimeInterface
    {
        return $this->Date_Reponse;
    }

    public function setDate_Reponse(?\DateTimeInterface $Date_Reponse): self
    {
        $this->Date_Reponse = $Date_Reponse;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
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

    public function getIDReponse(): ?int
    {
        return $this->ID_Reponse;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->Date_Reponse;
    }

    public function setDateReponse(?\DateTimeInterface $Date_Reponse): static
    {
        $this->Date_Reponse = $Date_Reponse;

        return $this;
    }

}
