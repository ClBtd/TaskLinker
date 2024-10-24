<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Task;
use App\Enum\TaskStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre de la tâche'
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Description'
            ])
            ->add('deadline', null, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date',
            ])
            ->add('status', EnumType::class, [
                'class'  => TaskStatus::class,
                'label' => 'Statut',
            ])
            ->add('employee', EntityType::class, [
                'class' => Employee::class,
                'required' => false,
                'choice_label' => function (Employee $employee) {
                    return $employee->getFirstName() . ' ' . $employee->getLastName();
                },
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'required' => false,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
