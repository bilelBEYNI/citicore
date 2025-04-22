<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\DemandeRepository;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
#[ORM\Table(name: 'demande')]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $Demande_id = null;

    public function getDemande_id(): ?int
    {
        return $this->Demande_id;
    }

    public function setDemande_id(int $Demande_id): self
    {
        $this->Demande_id = $Demande_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $Utilisateur_id = null;

    public function getUtilisateur_id(): ?int
    {
        return $this->Utilisateur_id;
    }

    public function setUtilisateur_id(int $Utilisateur_id): self
    {
        $this->Utilisateur_id = $Utilisateur_id;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $contenu = null;

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_demande = null;

    public function getDate_demande(): ?\DateTimeInterface
    {
        return $this->date_demande;
    }

    public function setDate_demande(\DateTimeInterface $date_demande): self
    {
        $this->date_demande = $date_demande;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

}
