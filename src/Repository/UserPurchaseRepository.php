<?php

namespace App\Repository;

use App\Entity\UserPurchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class UserPurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPurchase::class);
    }

    /**
     * Vérifie si un utilisateur a acheté une leçon donnée
     */
    public function hasPurchasedLecon($user, $leconId): bool
    {
        $query = $this->createQueryBuilder('up')
            ->andWhere('up.user = :user')
            ->andWhere('up.lecon = :lecon')
            ->setParameter('user', $user)
            ->setParameter('lecon', $leconId)
            ->getQuery()
            ->getOneOrNullResult();

        return $query !== null;
    }

public function getPurchasedLecons($user)
{
    return $this->createQueryBuilder('purchase')
        ->join('purchase.lecon', 'lecon') // Joindre la relation "lecon"
        ->where('purchase.user = :user') // Condition sur l'utilisateur
        ->setParameter('user', $user)
        ->select('lecon') // Sélectionner uniquement les informations de "lecon"
        ->addSelect('purchase') // Inclure explicitement l'entité Racine pour respecter le DQL
        ->getQuery()
        ->getResult();
}
public function getPurchasedCursus($user)
{
    return $this->createQueryBuilder('purchase')
        ->join('purchase.cursus', 'cursus') // Joindre la relation "cursus"
        ->where('purchase.user = :user') // Condition sur l'utilisateur
        ->setParameter('user', $user)
        ->select('cursus') // Sélectionner uniquement les cursus
        ->addSelect('purchase') // Inclure explicitement la racine "purchase" pour DQL
        ->getQuery()
        ->getResult();
}
public function getPurchasedTheme(User $user): array
       {
           $queryBuilder = $this->createQueryBuilder('purchase')
               ->leftJoin('purchase.theme', 'theme') // Jointure avec l'entité liée (par exemple Theme)
               ->andWhere('purchase.user = :user')
               ->setParameter('user', $user)
               ->select('theme')
               ->getQuery();

           return $queryBuilder->getResult();
       }
}