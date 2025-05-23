<?php

namespace App\Repository;

use App\Entity\Agence;
use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voiture>
 *
 * @method Voiture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voiture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voiture[]    findAll()
 * @method Voiture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    //    /**
    //     * @return Voiture[] Returns an array of Voiture objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Voiture
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findBycriteres($data): array
    {
        $queryBuilder = $this->createQueryBuilder('v');

        if (!empty($data['marque'])) {
            $queryBuilder->andWhere('v.marque = :marque')
                ->setParameter('marque', $data['marque']);
        }

        if (!empty($data['modele'])) {
            $queryBuilder->andWhere('v.modele = :modele')
                ->setParameter('modele', $data['modele']);
        }

        if (!empty($data['agence'])) {
            $queryBuilder->andWhere('v.agence = :agence')
                ->setParameter('agence', $data['agence']);
        }

        if (!empty($data['prixVente'])) {
            $queryBuilder->andWhere('v.prixVente <= :prix')
                ->setParameter('prix', $data['prixVente']);
        }

        if (!empty($data['kilometrage'])) {
            $queryBuilder->andWhere('v.kilometrage <= :kilometrage')
                ->setParameter('kilometrage', $data['kilometrage']);
        }

        $queryBuilder->andWhere('v.estALouer = false');
        $queryBuilder->andWhere('v.estEnVente = true');

        return $queryBuilder->getQuery()->getResult();
    }

    public function findNumberVente($nombre): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.estEnVente = true')
            ->orderBy('v.id', 'ASC')
            ->setMaxResults($nombre)
            ->getQuery()
            ->getResult()
           ;
    }

    public function findAllVente(): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.estEnVente = true')
            ->orderBy('v.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findNumberLocation($number): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.estALouer = true')
            ->orderBy('v.id', 'ASC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllLocation(): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.estALouer = true')
            ->getQuery()
            ->getResult();
    }
    public function findAllDisponibleLocation(Agence $agence, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): ?array
    {
        return $this->createQueryBuilder('v')
            ->leftJoin('v.locations', 'l')
            ->andWhere('v.agence = :agence')
            ->andWhere('v.estALouer = true')
            ->andWhere('v.estEnVente = false')
            ->andWhere('(
            (l.dateDebutLocation > :dateFin OR l.dateFinLocation < :dateDebut) OR
            (l.dateDebutLocation IS NULL AND l.dateFinLocation IS NULL)
        )')
            ->setParameter('agence', $agence)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }

    public function findOneByIdDispoVente($id){
        return $this->createQueryBuilder('v')
                    ->where('v.id = :id')
                    ->andWhere('v.estALouer = false and v.estEnVente = true')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
