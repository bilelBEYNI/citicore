<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Commande_produit;

#[ORM\Entity]
class Produit
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_produit;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "float")]
    private float $prix;

    #[ORM\Column(type: "integer")]
    private int $vendeur_id;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_ajout;

    #[ORM\Column(type: "string", length: 255)]
    private string $photo;

    public function getId_produit()
    {
        return $this->id_produit;
    }

    public function setId_produit($value)
    {
        $this->id_produit = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setPrix($value)
    {
        $this->prix = $value;
    }

    public function getVendeur_id()
    {
        return $this->vendeur_id;
    }

    public function setVendeur_id($value)
    {
        $this->vendeur_id = $value;
    }

    public function getDate_ajout()
    {
        return $this->date_ajout;
    }

    public function setDate_ajout($value)
    {
        $this->date_ajout = $value;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($value)
    {
        $this->photo = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_produit", targetEntity: Commande_produit::class)]
    private Collection $commande_produits;

        public function getCommande_produits(): Collection
        {
            return $this->commande_produits;
        }
    
        public function addCommande_produit(Commande_produit $commande_produit): self
        {
            if (!$this->commande_produits->contains($commande_produit)) {
                $this->commande_produits[] = $commande_produit;
                $commande_produit->setId_produit($this);
            }
    
            return $this;
        }
    
        public function removeCommande_produit(Commande_produit $commande_produit): self
        {
            if ($this->commande_produits->removeElement($commande_produit)) {
                // set the owning side to null (unless already changed)
                if ($commande_produit->getId_produit() === $this) {
                    $commande_produit->setId_produit(null);
                }
            }
    
            return $this;
        }
}
