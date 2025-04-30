<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Avis
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $avis_id;

    #[ORM\Column(type: "integer")]
    private int $Utilisateur_id;

    #[ORM\Column(type: "text")]
    private string $commentaire;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_avis;

    #[ORM\Column(type: "integer")]
    private int $Demande_id;

    public function getAvis_id()
    {
        return $this->avis_id;
    }

    public function setAvis_id($value)
    {
        $this->avis_id = $value;
    }

    public function getUtilisateur_id()
    {
        return $this->Utilisateur_id;
    }

    public function setUtilisateur_id($value)
    {
        $this->Utilisateur_id = $value;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }

    public function setCommentaire($value)
    {
        $this->commentaire = $value;
    }

    public function getDate_avis()
    {
        return $this->date_avis;
    }

    public function setDate_avis($value)
    {
        $this->date_avis = $value;
    }

    public function getDemande_id()
    {
        return $this->Demande_id;
    }

    public function setDemande_id($value)
    {
        $this->Demande_id = $value;
    }
}
