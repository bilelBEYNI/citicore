<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Evenement;

#[ORM\Entity]
class Categorie
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $categorie_id;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom_categorie;

    #[ORM\Column(type: "string", length: 255)]
    private string $description;

    #[ORM\Column(type: "string", length: 255)]
    private string $imageUrl;

    public function getCategorie_id()
    {
        return $this->categorie_id;
    }

    public function setCategorie_id($value)
    {
        $this->categorie_id = $value;
    }

    public function getNom_categorie()
    {
        return $this->nom_categorie;
    }

    public function setNom_categorie($value)
    {
        $this->nom_categorie = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function setImageUrl($value)
    {
        $this->imageUrl = $value;
    }

    #[ORM\OneToMany(mappedBy: "categorie_id", targetEntity: Evenement::class)]
    private Collection $evenements;

        public function getEvenements(): Collection
        {
            return $this->evenements;
        }
    
        public function addEvenement(Evenement $evenement): self
        {
            if (!$this->evenements->contains($evenement)) {
                $this->evenements[] = $evenement;
                $evenement->setCategorie_id($this);
            }
    
            return $this;
        }
    
        public function removeEvenement(Evenement $evenement): self
        {
            if ($this->evenements->removeElement($evenement)) {
                // set the owning side to null (unless already changed)
                if ($evenement->getCategorie_id() === $this) {
                    $evenement->setCategorie_id(null);
                }
            }
    
            return $this;
        }
}
