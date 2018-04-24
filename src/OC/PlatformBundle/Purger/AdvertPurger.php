<?php

namespace OC\PlatformBundle\Purger;

use Doctrine\ORM\EntityManagerInterface;

class AdvertPurger
{
    /**
     * @var EntityManagerInterface Le service permettant l'accès aux entités (Doctrine)
     */
    private $_em;

    /**
     * AdvertPurger constructor.
     * @param EntityManagerInterface $entityManager Injection de dépendance : le service permettant l'accès aux entités (Doctrine)
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_em = $entityManager;
    }

    /**
     * Cette méthode permet de supprimer les annonces (Advert) n'ayant aucune candidature (Application)
     * et dont la date (Advert::updatedAt || Advert::date) est supérieure à $days
     *
     * @param int $days Le nombre de jours
     * @return mixed
     */
    public function purge($days)
    {

//        Conversion du paramètre $days en DateTime
        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        
//        Récupération de la liste des Advert à purger
        $listAdverts = $this->_em->getRepository('OCPlatformBundle:Advert')->getAdvertsToPurge($date);

//        On récupère le nombre d'annonces à supprimer, à retourner au controller
        $nbPurgedAdverts = count($listAdverts);

        /**
         * Plus besoin, la relation Advert <=> AdvertSkill a été passée en bidirectionnelle !
         *
         * @see OC\PlatformBundle\Entity\Advert
         * @see OC\PlatformBundle\Entity\AdvertSkill
         */

//        Pour respecter les contraintes de clés étrangères de la BDD, on doit d'abord supprimer les références de AdvertSkill
//        On récupère les AdvertSkill dépendant des Advert à purger
//        $listAdvertSkills = $this->_em->getRepository('OCPlatformBundle:AdvertSkill')->getFromAdverts($listAdverts);

//        On boucle pour les supprimer
//        foreach($listAdvertSkills as $advertSkill) {
//            $this->_em->remove($advertSkill);
//        }

//        Maintenant, on peut supprimer directement les Advert
        foreach($listAdverts as $advert) {
            $this->_em->remove($advert);
        }

//        On enregistre les modifications
        $this->_em->flush();

        return $nbPurgedAdverts;
    }
}