<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\AviRepository;

#[ORM\Entity(repositoryClass: AviRepository::class)]
#[ORM\Table(name: 'avis')]
class Avi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $avis_id = null;

    public function getAvis_id(): ?int
    {
        return $this->avis_id;
    }

    public function setAvis_id(int $avis_id): self
    {
        $this->avis_id = $avis_id;
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
    private ?string $commentaire = null;

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_avis = null;

    public function getDate_avis(): ?\DateTimeInterface
    {
        return $this->date_avis;
    }

    public function setDate_avis(\DateTimeInterface $date_avis): self
    {
        $this->date_avis = $date_avis;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
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

    public function getAvisId(): ?int
    {
        return $this->avis_id;
    }

    public function getUtilisateurId(): ?int
    {
        return $this->Utilisateur_id;
    }

    public function setUtilisateurId(int $Utilisateur_id): static
    {
        $this->Utilisateur_id = $Utilisateur_id;

        return $this;
    }

    public function getDateAvis(): ?\DateTimeInterface
    {
        return $this->date_avis;
    }

    public function setDateAvis(\DateTimeInterface $date_avis): static
    {
        $this->date_avis = $date_avis;

        return $this;
    }

    public function getDemandeId(): ?int
    {
        return $this->Demande_id;
    }

    public function setDemandeId(int $Demande_id): static
    {
        $this->Demande_id = $Demande_id;

        return $this;
    }

}
