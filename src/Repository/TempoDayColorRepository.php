<?php

namespace App\Repository;

use App\Entity\TempoDayColor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TempoDayColor>
 *
 * @method TempoDayColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method TempoDayColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method TempoDayColor[]    findAll()
 * @method TempoDayColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TempoDayColorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TempoDayColor::class);
    }

    public function save(TempoDayColor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TempoDayColor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCachedTempoDayColor(): ?TempoDayColor
    {
        $date = new \DateTime;
        if ($date->format('G') >= 11) { // Les données tempo du lendemain ne sont disponibles qu'à partir de 11h environ
            $date->modify('+ 1 day');
        }
        return $this->createQueryBuilder('t')->where('t.day = :day')->setParameter('day', $date, Types::DATE_MUTABLE)->getQuery()->getOneOrNullResult();
    }
}