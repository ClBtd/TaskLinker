<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/task', name: 'app_task')]
class TaskController extends AbstractController
{
    #[Route('/{id}/edit', name: '_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_project_not_allowed'); 
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();
            return $this->redirectToRoute('app_project_show', ['id' => $task->getProject()->getId()]);;
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form
        ]);
    }

    #[Route('/{id}/{status}/new', name: '_new', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(string $status, Project $project, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_project_not_allowed'); 
        }

        $task = new Task();
        $task->setStatus(TaskStatus::from($status));
        $task->setProject($project);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            if ($task->getEmployee()) {
                $project->addEmployee($task->getEmployee());
            }
            
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);;
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(Task $task, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_project_not_allowed'); 
        }

        if ($task->getEmployee()) {
            $task->getProject()->removeEmployee($task->getEmployee());
        }
        
        $em->remove($task);
        $em->flush();
        
        return $this->redirectToRoute('app_project_show', ['id' => $task->getProject()->getId()]);
    }
}
