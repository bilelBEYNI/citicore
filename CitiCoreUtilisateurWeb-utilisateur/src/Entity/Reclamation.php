<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Reponse;

#[ORM\Entity]
class Reclamation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $ID_Reclamation;

    #[ORM\Column(type: "string", length: 255)]
    private string $Sujet;

    #[ORM\Column(type: "text")]
    private string $Description;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $Date_Creation;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $Date_Resolution;

    #[ORM\Column(type: "string", length: 20)]
    private string $Type_Reclamation;

    #[ORM\Column(type: "integer")]
    private int $Cin_Utilisateur;

    public function getID_Reclamation()
    {
        return $this->ID_Reclamation;
    }

    public function setID_Reclamation($value)
    {
        $this->ID_Reclamation = $value;
    }

    public function getSujet()
    {
        return $this->Sujet;
    }

    public function setSujet($value)
    {
        $this->Sujet = $value;
    }

    public function getDescription()
    {
        return $this->Description;
    }

    public function setDescription($value)
    {
        $this->Description = $value;
    }

    public function getDate_Creation()
    {
        return $this->Date_Creation;
    }

    public function setDate_Creation($value)
    {
        $this->Date_Creation = $value;
    }

    public function getDate_Resolution()
    {
        return $this->Date_Resolution;
    }

    public function setDate_Resolution($value)
    {
        $this->Date_Resolution = $value;
    }

    public function getType_Reclamation()
    {
        return $this->Type_Reclamation;
    }

    public function setType_Reclamation($value)
    {
        $this->Type_Reclamation = $value;
    }

    public function getCin_Utilisateur()
    {
        return $this->Cin_Utilisateur;
    }

    public function setCin_Utilisateur($value)
    {
        $this->Cin_Utilisateur = $value;
    }

    #[ORM\OneToMany(mappedBy: "ID_Reclamation", targetEntity: Reponse::class)]
    private Collection $reponses;

        public function getReponses(): Collection
        {
            return $this->reponses;
        }
    
        public function addReponse(Reponse $reponse): self
        {
            if (!$this->reponses->contains($reponse)) {
                $this->reponses[] = $reponse;
                $reponse->setID_Reclamation($this);
            }
    
            return $this;
        }
    
        public function removeReponse(Reponse $reponse): self
        {
            if ($this->reponses->removeElement($reponse)) {
                // set the owning side to null (unless already changed)
                if ($reponse->getID_Reclamation() === $this) {
                    $reponse->setID_Reclamation(null);
                }
            }
    
            return $this;
        }
}
