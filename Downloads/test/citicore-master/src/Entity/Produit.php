<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ProduitRepository;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produit')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_produit = null;

    public function getId_produit(): ?int
    {
        return $this->id_produit;
    }

    public function setId_produit(int $id_produit): self
    {
        $this->id_produit = $id_produit;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?float $prix = null;

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $vendeur_id = null;

    public function getVendeur_id(): ?int
    {
        return $this->vendeur_id;
    }

    public function setVendeur_id(int $vendeur_id): self
    {
        $this->vendeur_id = $vendeur_id;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_ajout = null;

    public function getDate_ajout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDate_ajout(\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $photo = null;

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    #[ORM\ManyToMany(targetEntity: Commande::class, inversedBy: 'produits')]
    #[ORM\JoinTable(
        name: 'commande_produit',
        joinColumns: [
            new ORM\JoinColumn(name: 'id_produit', referencedColumnName: 'id_produit')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'id_commande', referencedColumnName: 'id_commande')
        ]
    )]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        if (!$this->commandes instanceof Collection) {
            $this->commandes = new ArrayCollection();
        }
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->getCommandes()->contains($commande)) {
            $this->getCommandes()->add($commande);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        $this->getCommandes()->removeElement($commande);
        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->id_produit;
    }

    public function getVendeurId(): ?int
    {
        return $this->vendeur_id;
    }

    public function setVendeurId(int $vendeur_id): static
    {
        $this->vendeur_id = $vendeur_id;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): static
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->getIdProduit(); // ou retourner directement $this->id_produit
    }
    
}
