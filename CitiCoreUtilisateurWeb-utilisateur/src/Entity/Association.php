<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Association
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_association;

    #[ORM\Column(type: "string", length: 255)]
    private string $Nom;

    #[ORM\Column(type: "string", length: 255)]
    private string $Email;

    #[ORM\Column(type: "string", length: 15)]
    private string $Telephone;

    #[ORM\Column(type: "text")]
    private string $Description;

    #[ORM\Column(type: "text")]
    private string $Adresse;

    public function getId_association()
    {
        return $this->id_association;
    }

    public function setId_association($value)
    {
        $this->id_association = $value;
    }

    public function getNom()
    {
        return $this->Nom;
    }

    public function setNom($value)
    {
        $this->Nom = $value;
    }

    public function getEmail()
    {
        return $this->Email;
    }

    public function setEmail($value)
    {
        $this->Email = $value;
    }

    public function getTelephone()
    {
        return $this->Telephone;
    }

    public function setTelephone($value)
    {
        $this->Telephone = $value;
    }

    public function getDescription()
    {
        return $this->Description;
    }

    public function setDescription($value)
    {
        $this->Description = $value;
    }

    public function getAdresse()
    {
        return $this->Adresse;
    }

    public function setAdresse($value)
    {
        $this->Adresse = $value;
    }
}
