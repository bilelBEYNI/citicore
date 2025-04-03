<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FeedbackRepository;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ORM\Table(name: 'feedback')]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_FeedBack = null;

    public function getId_FeedBack(): ?int
    {
        return $this->id_FeedBack;
    }

    public function setId_FeedBack(int $id_FeedBack): self
    {
        $this->id_FeedBack = $id_FeedBack;
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

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'feedbacksParticipant')]
    #[ORM\JoinColumn(name: 'Cin_Participant', referencedColumnName: 'Cin')]
    private ?Utilisateur $participant = null;

    public function getParticipant(): ?Utilisateur
    {
        return $this->participant;
    }

    public function setParticipant(?Utilisateur $participant): self
    {
        $this->participant = $participant;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'feedbacksOrganisateur')]
    #[ORM\JoinColumn(name: 'Cin_Organisateur', referencedColumnName: 'Cin')]
    private ?Utilisateur $organisateur = null;

    public function getOrganisateur(): ?Utilisateur
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Utilisateur $organisateur): self
    {
        $this->organisateur = $organisateur;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_creation = null;

    public function getDate_creation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDate_creation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getIdFeedBack(): ?int
    {
        return $this->id_FeedBack;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }
}
