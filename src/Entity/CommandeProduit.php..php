<?php 
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="commande_produit")
 */
class CommandeProduit
{
    /** 
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="lignes")
     * @ORM\JoinColumn(nullable=false, name="id_commande", referencedColumnName="id_commande")
     */
    private $commande;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class)
     * @ORM\JoinColumn(nullable=false, name="id_produit", referencedColumnName="id_produit")
     */
    private $produit;

    /** @ORM\Column(type="integer") */
    private $quantite;

    /** @ORM\Column(type="decimal", scale=2) */
    private $prixUnitaire;

    // getters & setters…
    public function getId(): ?int
{
    return $this->id;
}

public function getCommande(): ?Commande
{
    return $this->commande;
}

public function setCommande(?Commande $commande): self
{
    $this->commande = $commande;
    return $this;
}

public function getProduit(): ?Produit
{
    return $this->produit;
}

public function setProduit(?Produit $produit): self
{
    $this->produit = $produit;
    return $this;
}

public function getQuantite(): ?int
{
    return $this->quantite;
}

public function setQuantite(int $quantite): self
{
    $this->quantite = $quantite;
    return $this;
}

public function getPrixUnitaire(): ?string
{
    return $this->prixUnitaire;
}

public function setPrixUnitaire(string $prixUnitaire): self
{
    $this->prixUnitaire = $prixUnitaire;
    return $this;
}

}
