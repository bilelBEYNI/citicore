<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FeedbackRepository;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ORM\Table(name: 'feedback')]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_FeedBack = null;

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $contenu = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'feedbacksParticipant')]
    #[ORM\JoinColumn(name: 'Cin_Participant', referencedColumnName: 'Cin')]
    private ?Utilisateur $participant = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'feedbacksOrganisateur')]
    #[ORM\JoinColumn(name: 'Cin_Organisateur', referencedColumnName: 'Cin')]
    private ?Utilisateur $organisateur = null;

    // Getters & Setters

    public function getId_FeedBack(): ?int
    {
        return $this->id_FeedBack;
    }

    public function setId_FeedBack(int $id_FeedBack): self
    {
        $this->id_FeedBack = $id_FeedBack;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getDate_creation(): ?DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDate_creation(DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getParticipant(): ?Utilisateur
    {
        return $this->participant;
    }

    public function setParticipant(?Utilisateur $participant): self
    {
        $this->participant = $participant;
        return $this;
    }

    public function getOrganisateur(): ?Utilisateur
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Utilisateur $organisateur): self
    {
        $this->organisateur = $organisateur;
        return $this;
    }
}
