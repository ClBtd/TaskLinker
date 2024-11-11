<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\TaskStatus;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/project', name: 'app_project')]
class ProjectController extends AbstractController
{

    #[Route('/', name: '')]
    public function index(ProjectRepository $repository): Response
    {
        $activeProjects = $repository->findActiveProjects();

        return $this->render('project/index.html.twig', [
           'projects' => $activeProjects 
        ]);
    }

    #[Route('/{id}', name: '_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: '_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(?Project $project, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_project_not_allowed'); 
        }

        $project ??= new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $project->setArchive(false);
            $em->persist($project);
            $em->flush();
            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);;
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form
        ]);
    }

    #[Route('/{id}/archive', name: '_archive', requirements: ['id' => '\d+'])]
    public function archive(Project $project, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_project_not_allowed'); 
        }

        $project->setArchive(true);
        foreach ($project->getTasks() as $task) {
            $project->removeTask($task);
        }
        $em->flush();
        return $this->redirectToRoute('app_project');
    }

    #[Route('/projet/acces', name: '_not_allowed')]
    public function redirect_unallowed_user(): Response
    {
        return $this->render('project/acces.html.twig');
    }
}
