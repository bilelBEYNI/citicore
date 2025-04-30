<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Projet_don
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_Projet_Don;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "float")]
    private float $montantRecu;

    #[ORM\Column(type: "float")]
    private float $objectif;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_debut;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_fin;

    #[ORM\Column(type: "integer")]
    private int $id_association;

    public function getId_Projet_Don()
    {
        return $this->id_Projet_Don;
    }

    public function setId_Projet_Don($value)
    {
        $this->id_Projet_Don = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getMontantRecu()
    {
        return $this->montantRecu;
    }

    public function setMontantRecu($value)
    {
        $this->montantRecu = $value;
    }

    public function getObjectif()
    {
        return $this->objectif;
    }

    public function setObjectif($value)
    {
        $this->objectif = $value;
    }

    public function getDate_debut()
    {
        return $this->date_debut;
    }

    public function setDate_debut($value)
    {
        $this->date_debut = $value;
    }

    public function getDate_fin()
    {
        return $this->date_fin;
    }

    public function setDate_fin($value)
    {
        $this->date_fin = $value;
    }

    public function getId_association()
    {
        return $this->id_association;
    }

    public function setId_association($value)
    {
        $this->id_association = $value;
    }
}
