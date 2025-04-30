<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Utilisateur;

#[ORM\Entity]
class Evenement
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')] 
    #[ORM\Column(type: 'integer')]
    private ?int $id_evenement = null;

        #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'categorie_id', onDelete: 'CASCADE')]
    private Categorie $categorie_id;

    
    private Utilisateur $organisateur_id;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom_evenement;

    #[ORM\Column(type: "string", length: 255)]
    private string $date_evenement;

    #[ORM\Column(type: "string", length: 255)]
    private string $lieu_evenement;

    public function getIdEvenement(): ?int
    {
        return $this->id_evenement;
    }

    public function setIdevenement($value)
    {
        $this->id_evenement = $value;
    }

    public function getCategorieid()
    {
        return $this->categorie_id;
    }

    public function setCategorieid($value)
    {
        $this->categorie_id = $value;
    }

    public function getOrganisateurid()
    {
        return $this->organisateur_id;
    }

    public function setOrganisateurid($value)
    {
        $this->organisateur_id = $value;
    }

    public function getNomevenement()
    {
        return $this->nom_evenement;
    }

    public function setNomevenement($value)
    {
        $this->nom_evenement = $value;
    }

    public function getDateevenement()
    {
        return $this->date_evenement;
    }

    public function setDateevenement($value)
    {
        $this->date_evenement = $value;
    }

    public function getLieuevenement()
    {
        return $this->lieu_evenement;
    }

    public function setLieuevenement($value)
    {
        $this->lieu_evenement = $value;
    }
}
