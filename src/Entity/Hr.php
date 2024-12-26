<?php

// src/Entity/Hr.php
namespace App\Entity;

use App\Repository\HrRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HrRepository::class)]
#[ORM\Table(name: "hr")]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorMap(["user" => "User", "employee" => "Employee", "hr" => "Hr", "owner" => "Owner"])]
class Hr extends User
{
    #[ORM\Column(type: "integer")]
    private ?int $hrId = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Employee>
     */
    #[ORM\OneToMany(targetEntity: Employee::class, mappedBy: 'Hr')]
    private Collection $employees;

    /**
     * @var Collection<int, VacationRequest>
     */
    #[ORM\OneToMany(targetEntity: VacationRequest::class, mappedBy: 'Hr')]
    private Collection $vacationRequests;

    /**
     * @var Collection<int, Equipement>
     */
    #[ORM\OneToMany(targetEntity: Equipement::class, mappedBy: 'Hr')]
    private Collection $equipements;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'Hr')]
    private Collection $events;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Owner $Owner = null;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->vacationRequests = new ArrayCollection();
        $this->equipements = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function getHrId(): ?int
    {
        return $this->hrId;
    }

    public function setHrId(int $hrId): static
    {
        $this->hrId = $hrId;
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

    /**
     * @return Collection<int, Employee>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(Employee $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setHr($this);
        }

        return $this;
    }

    public function removeEmployee(Employee $employee): static
    {
        if ($this->employees->removeElement($employee)) {
            // set the owning side to null (unless already changed)
            if ($employee->getHr() === $this) {
                $employee->setHr(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VacationRequest>
     */
    public function getVacationRequests(): Collection
    {
        return $this->vacationRequests;
    }

    public function addVacationRequest(VacationRequest $vacationRequest): static
    {
        if (!$this->vacationRequests->contains($vacationRequest)) {
            $this->vacationRequests->add($vacationRequest);
            $vacationRequest->setHr($this);
        }

        return $this;
    }

    public function removeVacationRequest(VacationRequest $vacationRequest): static
    {
        if ($this->vacationRequests->removeElement($vacationRequest)) {
            // set the owning side to null (unless already changed)
            if ($vacationRequest->getHr() === $this) {
                $vacationRequest->setHr(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
            $equipement->setHr($this);
        }

        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        if ($this->equipements->removeElement($equipement)) {
            // set the owning side to null (unless already changed)
            if ($equipement->getHr() === $this) {
                $equipement->setHr(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setHr($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getHr() === $this) {
                $event->setHr(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->Owner;
    }

    public function setOwner(?Owner $Owner): static
    {
        $this->Owner = $Owner;

        return $this;
    }
}
