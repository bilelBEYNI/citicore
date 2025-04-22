<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProduitRepository;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produit')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_produit = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le nom du produit est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', nullable: false)]
    #[Assert\NotNull(message: "Le prix est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être supérieur à 0.")]
    private ?float $prix = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotNull(message: "L'identifiant du vendeur est obligatoire.")]
    private ?int $vendeur_id = null;

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\Type(
        type: \DateTimeInterface::class,
        message: "La date d'ajout doit être une date valide."
    )]
    private \DateTimeInterface $date_ajout;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Url(message: "Le lien de la photo doit être une URL valide.")]
    private ?string $photo = null;

    #[ORM\ManyToMany(targetEntity: Commande::class, inversedBy: 'produits')]
    #[ORM\JoinTable(
        name: 'commande_produit',
        joinColumns: [new ORM\JoinColumn(name: 'id_produit', referencedColumnName: 'id_produit')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'id_commande', referencedColumnName: 'id_commande')]
    )]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->date_ajout = new \DateTime(); // 👈 Ajout automatique de la date du jour
    }

    // ==== Getters & Setters ====

    public function getId_produit(): ?int
    {
        return $this->id_produit;
    }

    public function setId_produit(int $id_produit): self
    {
        $this->id_produit = $id_produit;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getVendeur_id(): ?int
    {
        return $this->vendeur_id;
    }

    public function setVendeur_id(int $vendeur_id): self
    {
        $this->vendeur_id = $vendeur_id;
        return $this;
    }

    public function getDate_ajout(): \DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDate_ajout(\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        $this->commandes->removeElement($commande);
        return $this;
    }

    // Méthodes auxiliaires
    public function getIdProduit(): ?int
    {
        return $this->id_produit;
    }

    public function getVendeurId(): ?int
    {
        return $this->vendeur_id;
    }

    public function setVendeurId(int $vendeur_id): self
    {
        $this->vendeur_id = $vendeur_id;
        return $this;
    }

    public function getDateAjout(): \DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->getIdProduit();
    }
}
