<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use App\Form\RegistartionType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
    {
        $employee = new Employee();
        $employee->setStatus(EmployeeStatus::CDI);
        $employee->setStartedAt(new DateTimeImmutable());
        $form = $this->createForm(RegistartionType::class, $employee);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $employee->setPassword(
                $userPasswordHasher->hashPassword(
                    $employee,
                    $form->get('password')->getData()
                )
            );

            $em->persist($employee);
            $em->flush();

            return $this->redirectToRoute('');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form
        ]);
    }
}
