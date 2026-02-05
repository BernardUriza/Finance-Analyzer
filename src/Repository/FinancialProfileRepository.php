<?php

namespace App\Repository;

use App\Entity\FinancialProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FinancialProfile>
 */
class FinancialProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FinancialProfile::class);
    }

    public function getOrCreate(): FinancialProfile
    {
        $profile = $this->findOneBy([], ['id' => 'ASC']);

        if (!$profile) {
            $profile = new FinancialProfile();
            $this->getEntityManager()->persist($profile);
            $this->getEntityManager()->flush();
        }

        return $profile;
    }
}
