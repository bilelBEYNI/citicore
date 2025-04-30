<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Commande_produit;

#[ORM\Entity]
class Commande
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_commande;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_commande;

    #[ORM\Column(type: "string", length: 200)]
    private string $status;

    public function getId_commande()
    {
        return $this->id_commande;
    }

    public function setId_commande($value)
    {
        $this->id_commande = $value;
    }

    public function getDate_commande()
    {
        return $this->date_commande;
    }

    public function setDate_commande($value)
    {
        $this->date_commande = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_commande", targetEntity: Commande_produit::class)]
    private Collection $commande_produits;

        public function getCommande_produits(): Collection
        {
            return $this->commande_produits;
        }
    
        public function addCommande_produit(Commande_produit $commande_produit): self
        {
            if (!$this->commande_produits->contains($commande_produit)) {
                $this->commande_produits[] = $commande_produit;
                $commande_produit->setId_commande($this);
            }
    
            return $this;
        }
    
        public function removeCommande_produit(Commande_produit $commande_produit): self
        {
            if ($this->commande_produits->removeElement($commande_produit)) {
                // set the owning side to null (unless already changed)
                if ($commande_produit->getId_commande() === $this) {
                    $commande_produit->setId_commande(null);
                }
            }
    
            return $this;
        }
}
