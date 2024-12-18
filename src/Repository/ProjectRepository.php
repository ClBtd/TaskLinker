<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findActiveProjects(): array 
    {
        return $this->createQueryBuilder('p')
            ->where('p.archive = :archive')
            ->setParameter('archive', false)
            ->getQuery()
            ->getResult()
            ;  
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findByEmployee($employee): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere(':employee MEMBER OF p.employee')
            ->setParameter('employee', $employee)
            ->getQuery()
            ->getResult()
            ;
    }
}
