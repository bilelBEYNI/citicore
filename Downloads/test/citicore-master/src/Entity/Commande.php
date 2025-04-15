<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commande')]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_commande', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'date_commande', type: 'date')]
    private ?\DateTimeInterface $date_commande = null;

    #[ORM\Column(name: 'status', type: 'string', length: 200)]
    private ?string $status = null;

    #[ORM\ManyToMany(targetEntity: Produit::class, mappedBy: 'commandes')]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->status = 'en_attente'; // ✅ valeur par défaut
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCommande(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTimeInterface $date): self
    {
        $this->date_commande = $date;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->addCommande($this); // synchronisation inverse
        }
        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removeCommande($this); // synchronisation inverse
        }
        return $this;
    }
}
