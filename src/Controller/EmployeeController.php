<?php
namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Task;
use App\Entity\VacationRequest;

use App\Repository\TaskRepository;
use App\Repository\VacationRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
// View Tasks for Employee
#[Route('/employee/{id}/tasks', name: 'employee_tasks')]
public function tasks(Employee $employee, TaskRepository $taskRepository): Response
{
if ($employee !== $this->getUser()) {
throw $this->createAccessDeniedException('Access Denied');
}

$tasks = $taskRepository->findBy(['employee' => $employee]);

return $this->render('employee/tasks.html.twig', [
'tasks' => $tasks,
]);
}

// Update Task Status
#[Route('/employee/{id}/task/{taskId}/update', name: 'employee_task_update')]
public function updateTask(Employee $employee, Task $task, Request $request, TaskRepository $taskRepository): Response
{
if ($task->getEmployee() !== $employee) {
throw $this->createAccessDeniedException('Access Denied');
}

$form = $this->createForm(Task::class, $task);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$taskRepository->save($task, true);
$this->addFlash('success', 'Task updated successfully.');
return $this->redirectToRoute('employee_tasks', ['id' => $employee->getId()]);
}

return $this->render('employee/task_update.html.twig', [
'form' => $form->createView(),
]);
}

// Request Vacation
#[Route('/employee/{id}/vacation/request', name: 'employee_vacation_request')]
public function requestVacation(Employee $employee, Request $request, VacationRequestRepository $vacationRequestRepository): Response
{
$vacationRequest = new VacationRequest();
$form = $this->createForm(VacationRequest::class, $vacationRequest);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$vacationRequest->setEmployee($employee);
$vacationRequestRepository->save($vacationRequest, true);
$this->addFlash('success', 'Vacation request submitted.');
return $this->redirectToRoute('employee_tasks', ['id' => $employee->getId()]);
}

return $this->render('employee/vacation_request.html.twig', [
'form' => $form->createView(),
]);
}

// Request Equipment
#[Route('/employee/{id}/equipment/request', name: 'employee_equipment_request')]
public function requestEquipment(Employee $employee, Request $request, EquipmentRepository $equipmentRepository): Response
{
$equipment = new Equipment();
$form = $this->createForm(Equipment::class, $equipment);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
$equipment->setEmployee($employee);
$equipmentRepository->save($equipment, true);
$this->addFlash('success', 'Equipment request submitted.');
return $this->redirectToRoute('employee_tasks', ['id' => $employee->getId()]);
}

return $this->render('employee/equipment_request.html.twig', [
'form' => $form->createView(),
]);
}
}