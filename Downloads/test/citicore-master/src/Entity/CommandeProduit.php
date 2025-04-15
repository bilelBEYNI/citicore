<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Commande;
use App\Entity\Produit;

#[ORM\Entity]
#[ORM\Table(name: "commande_produit")]
class CommandeProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class)]
    #[ORM\JoinColumn(name: "id_commande", referencedColumnName: "id_commande", nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: "id_produit", referencedColumnName: "id_produit", nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column(type: "integer")]
    private int $quantite = 0;

    // plus de mapping ORM ici : le prix est calculé dynamiquement
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;
        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    /**
     * Prix unitaire calculé à la volée depuis l'entité Produit.
     */
    public function getPrixUnitaire(): float
    {
        return $this->produit
            ? $this->produit->getPrix()
            : 0.0;
    }
}
