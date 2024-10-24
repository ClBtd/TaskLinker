<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee', name: 'app_employee')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(EmployeeRepository $repository): Response
    {
        $employees = $repository->findAll();

        return $this->render('employee/index.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Employee $employee, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($employee);
            $em->flush();
            return $this->redirectToRoute('app_employee');;
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form
        ]);
    }

    #[Route('/{id}/delete', name: '_delete', requirements: ['id' => '\d+'])]
    public function delete(Employee $employee, EntityManagerInterface $em): Response
    {
        foreach ($employee->getProjects() as $project) {
            $project->removeEmployee($employee);
        } 
        
        foreach ($employee->getTasks() as $task) {
            $task->removeEmployee();
        }

        $em->remove($employee);
        $em->flush();
        
        return $this->redirectToRoute('app_employee');
    }
}
