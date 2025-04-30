<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Reclamation;

#[ORM\Entity]
class Reponse
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $ID_Reponse;

        #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: "reponses")]
    #[ORM\JoinColumn(name: 'ID_Reclamation', referencedColumnName: 'ID_Reclamation', onDelete: 'CASCADE')]
    private Reclamation $ID_Reclamation;

    #[ORM\Column(type: "text")]
    private string $Contenu;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $Date_Reponse;

    #[ORM\Column(type: "string", length: 20)]
    private string $Statut;

    public function getID_Reponse()
    {
        return $this->ID_Reponse;
    }

    public function setID_Reponse($value)
    {
        $this->ID_Reponse = $value;
    }

    public function getID_Reclamation()
    {
        return $this->ID_Reclamation;
    }

    public function setID_Reclamation($value)
    {
        $this->ID_Reclamation = $value;
    }

    public function getContenu()
    {
        return $this->Contenu;
    }

    public function setContenu($value)
    {
        $this->Contenu = $value;
    }

    public function getDate_Reponse()
    {
        return $this->Date_Reponse;
    }

    public function setDate_Reponse($value)
    {
        $this->Date_Reponse = $value;
    }

    public function getStatut()
    {
        return $this->Statut;
    }

    public function setStatut($value)
    {
        $this->Statut = $value;
    }
}
