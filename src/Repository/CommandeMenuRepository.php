<?php

namespace App\Repository;

use App\Entity\CommandeMenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommandeMenu|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeMenu|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeMenu[]    findAll()
 * @method CommandeMenu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeMenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeMenu::class);
    }

    public function findByExampleField($id)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.user= :user_id')
            ->setParameter('user_id', $id)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return CommandeMenu[] Returns an array of CommandeMenu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommandeMenu
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
