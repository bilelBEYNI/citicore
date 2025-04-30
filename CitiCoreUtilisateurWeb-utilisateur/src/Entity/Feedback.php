<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Utilisateur;

#[ORM\Entity]
class Feedback
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_FeedBack;

    #[ORM\Column(type: "text")]
    private string $contenu;

        #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "feedbacks")]
    #[ORM\JoinColumn(name: 'Cin_Participant', referencedColumnName: 'Cin', onDelete: 'CASCADE')]
    private Utilisateur $Cin_Participant;

        #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "feedbacks")]
    #[ORM\JoinColumn(name: 'Cin_Organisateur', referencedColumnName: 'Cin', onDelete: 'CASCADE')]
    private Utilisateur $Cin_Organisateur;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_creation;

    public function getId_FeedBack()
    {
        return $this->id_FeedBack;
    }

    public function setId_FeedBack($value)
    {
        $this->id_FeedBack = $value;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($value)
    {
        $this->contenu = $value;
    }

    public function getCin_Participant()
    {
        return $this->Cin_Participant;
    }

    public function setCin_Participant($value)
    {
        $this->Cin_Participant = $value;
    }

    public function getCin_Organisateur()
    {
        return $this->Cin_Organisateur;
    }

    public function setCin_Organisateur($value)
    {
        $this->Cin_Organisateur = $value;
    }

    public function getDate_creation()
    {
        return $this->date_creation;
    }

    public function setDate_creation($value)
    {
        $this->date_creation = $value;
    }
}
