<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\AssociationRepository;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
#[ORM\Table(name: 'association')]
class Association
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_association = null;

    public function getId(): ?int
    {
        return $this->id_association;
    }

    public function setId_association(int $id_association): self
    {
        $this->id_association = $id_association;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "The name is required.")]
    private ?string $Nom = null;

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "The email is required.")]
    #[Assert\Email(message: "Please provide a valid email address.")]
    private ?string $Email = null;

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "The telephone is required.")]
    #[Assert\Length(
        max: 8,
        maxMessage: "The telephone number cannot exceed 8 characters."
    )]
    #[Assert\Regex(
        pattern: "/^\d+$/",
        message: "The telephone number must contain only digits."
    )]
    private ?string $Telephone = null;

    public function getTelephone(): ?string
    {
        return $this->Telephone;
    }

    public function setTelephone(string $Telephone): self
    {
        $this->Telephone = $Telephone;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "The description is required.")]
    private ?string $Description = null;

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "The address is required.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The address cannot exceed 255 characters."
    )]
    private ?string $Adresse = null;

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(?string $Adresse): self
    {
        $this->Adresse = $Adresse;
        return $this;
    }

    public function getIdAssociation(): ?int
    {
        return $this->id_association;
    }

}
