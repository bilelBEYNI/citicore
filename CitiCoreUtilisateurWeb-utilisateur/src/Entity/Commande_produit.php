<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Produit;

#[ORM\Entity]
class Commande_produit
{

    #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: "commande_produits")]
    #[ORM\JoinColumn(name: 'id_commande', referencedColumnName: 'id_commande', onDelete: 'CASCADE')]
    private Commande $id_commande;

    #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: "commande_produits")]
    #[ORM\JoinColumn(name: 'id_produit', referencedColumnName: 'id_produit', onDelete: 'CASCADE')]
    private Produit $id_produit;

    #[ORM\Column(type: "integer")]
    private int $quantite;

    public function getId_commande()
    {
        return $this->id_commande;
    }

    public function setId_commande($value)
    {
        $this->id_commande = $value;
    }

    public function getId_produit()
    {
        return $this->id_produit;
    }

    public function setId_produit($value)
    {
        $this->id_produit = $value;
    }

    public function getQuantite()
    {
        return $this->quantite;
    }

    public function setQuantite($value)
    {
        $this->quantite = $value;
    }
}
