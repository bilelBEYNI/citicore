<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $avis_id;

    #[ORM\Column(type: "integer")]
    private int $Utilisateur_id;

    #[ORM\Column(type: "text")]
    private string $commentaire;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_avis;

    #[ORM\ManyToOne(targetEntity: Demande::class, inversedBy: "avis")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Demande $demande = null;

    public function getAvis_id(): int
    {
        return $this->avis_id;
    }

    public function setAvis_id(int $avis_id): self
    {
        $this->avis_id = $avis_id;
        return $this;
    }

    public function getUtilisateur_id(): int
    {
        return $this->Utilisateur_id;
    }

    public function setUtilisateur_id(int $Utilisateur_id): self
    {
        $this->Utilisateur_id = $Utilisateur_id;
        return $this;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getDate_avis(): \DateTimeInterface
    {
        return $this->date_avis;
    }

    public function setDate_avis(\DateTimeInterface $date_avis): self
    {
        $this->date_avis = $date_avis;
        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): self
    {
        $this->demande = $demande;
        return $this;
    }
}
