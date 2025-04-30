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

    #[ORM\Column(type: "text")]
    private string $commentaire;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateavis = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $demandeId = null;

    public function __construct()
    {
        $this->dateavis = new \DateTime(); // Initialise avec la date actuelle
    }

    public function getAvis_id(): int
    {
        return $this->avis_id;
    }

    public function setAvis_id(int $avis_id): self
    {
        $this->avis_id = $avis_id;
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

    public function getDateavis(): ?\DateTimeInterface
    {
        return $this->dateavis;
    }

    public function setDateavis(?\DateTimeInterface $dateavis): self
    {
        $this->dateavis = $dateavis;
        return $this;
    }

    public function getDemandeId(): ?int
    {
        return $this->demandeId;
    }

    public function setDemandeId(?int $demandeId): self
    {
        $this->demandeId = $demandeId;
        return $this;
    }
}
