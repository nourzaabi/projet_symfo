<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\UserType;
use App\Form\TaskType;
use App\Form\EventType;
use App\Entity\User;
use App\Entity\Task;
use App\Entity\Event;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\QueryBuilder;


class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(UserRepository $userRepository, TaskRepository $taskRepository, EventRepository $eventRepository): Response
    {
        return $this->render('dashboard/Admin.html.twig', [
            'users' => $userRepository->findAll(),
            'tasks' => $taskRepository->findAll(),
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/dashboard/users', name: 'admin_manage_users')]
    public function manageUsers(Request $request, UserRepository $userRepository): Response
    {
        $page = (int) $request->query->get('page', 1);  // Default to page 1
        $limit = 5;  // Number of users per page

        // Calculate the offset for the query
        $offset = ($page - 1) * $limit;

        // Retrieve the paginated list of users
        $users = $userRepository->createQueryBuilder('u')
            ->setFirstResult($offset)  // Start from the calculated offset
            ->setMaxResults($limit)  // Limit to 5 users per page
            ->getQuery()
            ->getResult();

        // Count the total number of users
        $totalUsers = $userRepository->count([]);

        // Calculate the total number of pages
        $totalPages = ceil($totalUsers / $limit);

        // Pass the pagination data to the template
        return $this->render('dashboard/Admin.html.twig', [
            'users' => $users,
            'section' => 'users',
            'currentPage' => $page,  // Ensure this is passed to Twig
            'totalPages' => $totalPages,
        ]);
    }



    #[Route('/dashboard/tasks', name: 'admin_manage_tasks')]
    public function manageTasks(TaskRepository $taskRepository)
    {
        // Récupérer les tâches depuis la base de données
        $tasks = $taskRepository->findAll();

        // Passer la variable tasks au template
        return $this->render('dashboard/Admin.html.twig', [
            'tasks' => $tasks,
            'currentPage' => 1,
            'totalPages' => 7,
        ]);
    }

    #[Route('/dashboard/task/add', name: 'admin_add_task', methods: ['GET', 'POST'])]
    public function addTask(Request $request, TaskRepository $taskRepository)
    {
        $data = json_decode($request->getContent(), true);
        $task = new Task();
        $task->setDescription($data['description']);
        $task->setStatus($data['status']);
        $taskRepository->save($task); // Méthode pour sauvegarder dans la base de données

        return new JsonResponse($task);
    }

    #[Route('/dashboard/task/edit/{id}', name: 'admin_edit_task', methods: ['GET', 'POST'])]
    public function editTask($id, Request $request, TaskRepository $taskRepository)
    {
        $data = json_decode($request->getContent(), true);
        $task = $taskRepository->find($id);
        if ($task) {
            $task->setDescription($data['description']);
            $task->setStatus($data['status']);
            $taskRepository->save($task);
        }

        return new JsonResponse($task);
    }

    #[Route('/dashboard/task/delete/{id}', name: 'admin_delete_task', methods: ['POST'])]
    public function deleteTask(Request $request, Task $task): Response
    {
        if (!$this->isCsrfTokenValid('delete_task', $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();
        $this->addFlash('success', 'Task deleted successfully.');

        return $this->redirectToRoute('admin_manage_tasks');
    }


    #[Route('/dashboard/events', name: 'admin_manage_events')]
    public function manageEvents(EventRepository $eventRepository): Response
    {
        return $this->render('dashboard/Admin.html.twig', [
            'events' => $eventRepository->findAll(),
            'section' => 'events',
        ]);
    }

    #[Route('/dashboard/user/add', name: 'admin_add_user', methods: ['POST'])]
    public function addUser(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('add_user', $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $username = $request->request->get('username');
        $role = $request->request->get('role');
        $status = $request->request->get('status');
        $plainPassword = $request->request->get('password');

        if (!$username || !$role || !$status || !$plainPassword) {
            $this->addFlash('error', 'All fields are required.');
            return $this->redirectToRoute('admin_manage_users');
        }

        $user = new User();
        $user->setUsername($username);
        $user->setRole($role);
        $user->setStatus($status);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'User added successfully.');
        return $this->redirectToRoute('admin_manage_users');
    }


    #[Route('/dashboard/user/edit/{id}', name: 'admin_edit_user', methods: ['GET', 'POST'])]
    public function editUser(Request $request, User $user): Response
    {
        if (!$this->isCsrfTokenValid('edit_user', $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        // Récupérer les données du formulaire
        $username = $request->request->get('username');
        $role = $request->request->get('role');
        $status = $request->request->get('status');
        $plainPassword = $request->request->get('password');

        // Validation des champs requis
        if (!$username || !$role || !$status) {
            $this->addFlash('error', 'Tous les champs sauf le mot de passe sont requis.');
            return $this->redirectToRoute('admin_manage_users');
        }

        // Mise à jour des informations de l'utilisateur
        $user->setUsername($username);
        $user->setRole($role);
        $user->setStatus($status);

        // Si un mot de passe est fourni, le hacher et l'ajouter
        if ($plainPassword) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        // Sauvegarder les modifications dans la base de données
        $this->entityManager->flush();

        $this->addFlash('success', 'Utilisateur mis à jour avec succès.');
        return $this->redirectToRoute('admin_manage_users');
    }


    #[Route('/dashboard/user/delete/{id}', name: 'admin_delete_user', methods: ['GET'])]
    public function deleteUser(User $user): Response
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_manage_users');
    }


}
