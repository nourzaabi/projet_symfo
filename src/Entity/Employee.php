<?php
// src/Entity/Employee.php
namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\Table(name: "employee")]
#[ORM\InheritanceType("JOINED")]
class Employee extends User
{
    #[ORM\Column]
    private ?int $employeeId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $tasks = [];

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $vacationRequests = [];

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Owner $Owner = null;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\ManyToMany(targetEntity: Task::class, inversedBy: 'employees')]
    private Collection $Task;

    /**
     * @var Collection<int, VacationRequest>
     */
    #[ORM\ManyToMany(targetEntity: VacationRequest::class, inversedBy: 'employees')]
    private Collection $VacationRequest;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hr $Hr = null;

    public function __construct()
    {
        $this->Task = new ArrayCollection();
        $this->VacationRequest = new ArrayCollection();
    }

    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function setEmployeeId(int $employeeId): static
    {
        $this->employeeId = $employeeId;
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

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function setTasks(array $tasks): static
    {
        $this->tasks = $tasks;
        return $this;
    }

    public function getVacationRequests(): array
    {
        return $this->vacationRequests;
    }

    public function setVacationRequests(array $vacationRequests): static
    {
        $this->vacationRequests = $vacationRequests;
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

    /**
     * @return Collection<int, Task>
     */
    public function getTask(): Collection
    {
        return $this->Task;
    }

    public function addTask(Task $task): static
    {
        if (!$this->Task->contains($task)) {
            $this->Task->add($task);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        $this->Task->removeElement($task);

        return $this;
    }

    /**
     * @return Collection<int, VacationRequest>
     */
    public function getVacationRequest(): Collection
    {
        return $this->VacationRequest;
    }

    public function addVacationRequest(VacationRequest $vacationRequest): static
    {
        if (!$this->VacationRequest->contains($vacationRequest)) {
            $this->VacationRequest->add($vacationRequest);
        }

        return $this;
    }

    public function removeVacationRequest(VacationRequest $vacationRequest): static
    {
        $this->VacationRequest->removeElement($vacationRequest);

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
