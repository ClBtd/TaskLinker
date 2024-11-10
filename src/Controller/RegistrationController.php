<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use App\Form\RegistrationType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/registration', name: 'app_registration')]
class RegistrationController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
    {
        return $this->render('registration/index.html.twig');
    }
    

    #[Route('/new', name: '_new')]
    public function registration(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): Response
    {
        $employee = new Employee();
        $employee->setStatus(EmployeeStatus::CDI);
        $employee->setStartedAt(new DateTimeImmutable());
        $employee->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationType::class, $employee);
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

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/new.html.twig', [
            'form' => $form
        ]);
    }
}
