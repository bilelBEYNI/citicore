<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UtilisateurRepository;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $Cin = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Nom = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Prenom = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Num_Tel = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Email = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Genre = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Photo_Utilisateur = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Mot_De_Passe = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Role = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Token = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $failed_attempts = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $ban_time = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $login_failures = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $is_banned = null;

    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'utilisateur')]
    private Collection $evenements;

    #[ORM\OneToMany(targetEntity: Feedback::class, mappedBy: 'participant')]
    private Collection $feedbacksParticipant;

    #[ORM\OneToMany(targetEntity: Feedback::class, mappedBy: 'organisateur')]
    private Collection $feedbacksOrganisateur;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->feedbacksParticipant = new ArrayCollection();
        $this->feedbacksOrganisateur = new ArrayCollection();
    }

    public function getCin(): ?int
    {
        return $this->Cin;
    }

    public function setCin(int $Cin): self
    {
        $this->Cin = $Cin;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;
        return $this;
    }

    public function getNum_Tel(): ?string
    {
        return $this->Num_Tel;
    }

    public function setNum_Tel(string $Num_Tel): self
    {
        $this->Num_Tel = $Num_Tel;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;
        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->Genre;
    }

    public function setGenre(string $Genre): self
    {
        $this->Genre = $Genre;
        return $this;
    }

    public function getPhoto_Utilisateur(): ?string
    {
        return $this->Photo_Utilisateur;
    }

    public function setPhoto_Utilisateur(string $Photo_Utilisateur): self
    {
        $this->Photo_Utilisateur = $Photo_Utilisateur;
        return $this;
    }

    public function getMot_De_Passe(): ?string
    {
        return $this->Mot_De_Passe;
    }

    public function setMot_De_Passe(string $Mot_De_Passe): self
    {
        $this->Mot_De_Passe = $Mot_De_Passe;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->Role;
    }

    public function setRole(string $Role): self
    {
        $this->Role = $Role;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->Token;
    }

    public function setToken(?string $Token): self
    {
        $this->Token = $Token;
        return $this;
    }

    public function getFailed_attempts(): ?int
    {
        return $this->failed_attempts;
    }

    public function setFailed_attempts(?int $failed_attempts): self
    {
        $this->failed_attempts = $failed_attempts;
        return $this;
    }

    public function getBan_time(): ?\DateTimeInterface
    {
        return $this->ban_time;
    }

    public function setBan_time(\DateTimeInterface $ban_time): self
    {
        $this->ban_time = $ban_time;
        return $this;
    }

    public function getLogin_failures(): ?int
    {
        return $this->login_failures;
    }

    public function setLogin_failures(?int $login_failures): self
    {
        $this->login_failures = $login_failures;
        return $this;
    }

    public function is_banned(): ?bool
    {
        return $this->is_banned;
    }

    public function setIs_banned(?bool $is_banned): self
    {
        $this->is_banned = $is_banned;
        return $this;
    }

    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        $this->evenements[] = $evenement;
        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        $this->evenements->removeElement($evenement);
        return $this;
    }

    public function getFeedbacksParticipant(): Collection
    {
        return $this->feedbacksParticipant;
    }

    public function addFeedbackParticipant(Feedback $feedback): self
    {
        if (!$this->feedbacksParticipant->contains($feedback)) {
            $this->feedbacksParticipant[] = $feedback;
        }
        return $this;
    }

    public function removeFeedbackParticipant(Feedback $feedback): self
    {
        $this->feedbacksParticipant->removeElement($feedback);
        return $this;
    }

    public function getFeedbacksOrganisateur(): Collection
    {
        return $this->feedbacksOrganisateur;
    }

    public function addFeedbackOrganisateur(Feedback $feedback): self
    {
        if (!$this->feedbacksOrganisateur->contains($feedback)) {
            $this->feedbacksOrganisateur[] = $feedback;
        }
        return $this;
    }

    public function removeFeedbackOrganisateur(Feedback $feedback): self
    {
        $this->feedbacksOrganisateur->removeElement($feedback);
        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->Num_Tel;
    }

    public function setNumTel(string $Num_Tel): static
    {
        $this->Num_Tel = $Num_Tel;

        return $this;
    }

    public function getPhotoUtilisateur(): ?string
    {
        return $this->Photo_Utilisateur;
    }

    public function setPhotoUtilisateur(string $Photo_Utilisateur): static
    {
        $this->Photo_Utilisateur = $Photo_Utilisateur;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->Mot_De_Passe;
    }

    public function setMotDePasse(string $Mot_De_Passe): static
    {
        $this->Mot_De_Passe = $Mot_De_Passe;

        return $this;
    }

    public function getFailedAttempts(): ?int
    {
        return $this->failed_attempts;
    }

    public function setFailedAttempts(?int $failed_attempts): static
    {
        $this->failed_attempts = $failed_attempts;

        return $this;
    }

    public function getBanTime(): ?\DateTimeInterface
    {
        return $this->ban_time;
    }

    public function setBanTime(\DateTimeInterface $ban_time): static
    {
        $this->ban_time = $ban_time;

        return $this;
    }

    public function getLoginFailures(): ?int
    {
        return $this->login_failures;
    }

    public function setLoginFailures(?int $login_failures): static
    {
        $this->login_failures = $login_failures;

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->is_banned;
    }

    public function setIsBanned(?bool $is_banned): static
    {
        $this->is_banned = $is_banned;

        return $this;
    }

    public function addFeedbacksParticipant(Feedback $feedbacksParticipant): static
    {
        if (!$this->feedbacksParticipant->contains($feedbacksParticipant)) {
            $this->feedbacksParticipant->add($feedbacksParticipant);
            $feedbacksParticipant->setParticipant($this);
        }

        return $this;
    }

    public function removeFeedbacksParticipant(Feedback $feedbacksParticipant): static
    {
        if ($this->feedbacksParticipant->removeElement($feedbacksParticipant)) {
            // set the owning side to null (unless already changed)
            if ($feedbacksParticipant->getParticipant() === $this) {
                $feedbacksParticipant->setParticipant(null);
            }
        }

        return $this;
    }

    public function addFeedbacksOrganisateur(Feedback $feedbacksOrganisateur): static
    {
        if (!$this->feedbacksOrganisateur->contains($feedbacksOrganisateur)) {
            $this->feedbacksOrganisateur->add($feedbacksOrganisateur);
            $feedbacksOrganisateur->setOrganisateur($this);
        }

        return $this;
    }

    public function removeFeedbacksOrganisateur(Feedback $feedbacksOrganisateur): static
    {
        if ($this->feedbacksOrganisateur->removeElement($feedbacksOrganisateur)) {
            // set the owning side to null (unless already changed)
            if ($feedbacksOrganisateur->getOrganisateur() === $this) {
                $feedbacksOrganisateur->setOrganisateur(null);
            }
        }

        return $this;
    }
}
