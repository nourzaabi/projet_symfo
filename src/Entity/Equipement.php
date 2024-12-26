<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $EquipementId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    private ?Hr $Hr = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipementId(): ?int
    {
        return $this->EquipementId;
    }

    public function setEquipementId(int $EquipementId): static
    {
        $this->EquipementId = $EquipementId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getHr(): ?Hr
    {
        return $this->Hr;
    }

    public function setHr(?Hr $Hr): static
    {
        $this->Hr = $Hr;

        return $this;
    }
}
