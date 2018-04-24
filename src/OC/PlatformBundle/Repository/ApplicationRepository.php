<?php

namespace OC\PlatformBundle\Repository;

/**
 * ApplicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends \Doctrine\ORM\EntityRepository
{
    public function getApplicationsWithAdvert($limit)
    {
        $qb = $this
            ->createQueryBuilder('app')
            ->innerJoin('app.advert', 'ad')
            ->addSelect('ad')
            ->setMaxResults((int)$limit)
        ;

        return $qb->getQuery()->getResult();
    }

    public function getApplicationsByAdvert($advert)
    {
        return $this
            ->createQueryBuilder('app')
            ->innerJoin('app.advert', 'ad')
            ->innerJoin('app.user', 'u')
            ->addSelect('u')
            ->where('app.advert = ?1')
            ->setParameter(1, $advert)
            ->getQuery()
            ->getResult()
        ;
    }
}
