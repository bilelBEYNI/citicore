<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $demandeId;
    

 
    
    #[ORM\Column(type: "integer")]     
    private int $cinUtilisateur;

    #[ORM\Column(type: "text")]
    private string $contenu;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateDemande = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $statut;

    #[ORM\OneToMany(mappedBy: "demande", targetEntity: Avis::class, cascade: ["persist", "remove"])]
    private Collection $avis;

    public function __construct()
    {
        $this->avis = new ArrayCollection();
    }

    public function getDemandeId(): int
    {
        return $this->demandeId;
    }

    public function setDemandeId(int $value): void
    {
        $this->demandeId = $value;
    }

    public function getCinUtilisateur(): int
    {
        return $this->cinUtilisateur;
    }

    public function setCinUtilisateur(int $value): void
    {
        $this->cinUtilisateur = $value;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $value): void
    {
        $this->contenu = $value;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTimeInterface $date): self
    {
        $this->dateDemande = $date;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $value): void
    {
        $this->statut = $value;
    }

    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function getFormattedDateDemande(): string
    {
        return $this->dateDemande ? $this->dateDemande->format('Y-m-d') : '';
    }
}
