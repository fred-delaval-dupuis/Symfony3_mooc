<?php

namespace OC\PlatformBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Application;

class LoadAdvert extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $adverts = array(
            array(
                'title'     => 'Recherche développpeur Symfony2',
                'user'    => $this->getReference('user1'),
                'content'   => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
                'date'      => new \DateTime(),
                'updatedAt' => null,
                'app'       => true, // si true, 2 applications seront liées à l'annonce
            ),
            array(
                'title'     => 'Mission de webmaster',
                'user'    => $this->getReference('user1'),
                'content'   => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'      => new \DateTime(),
                'updatedAt' => null,
                'app'       => true,
            ),
            array(
                'title'     => 'Offre de stage webdesigner',
                'user'    => $this->getReference('user1'),
                'content'   => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'      => new \DateTime(),
                'updatedAt' => (new \DateTime())->sub(new \DateInterval('P1M')), // 1 month, ne devrait pas être supprimée lors de la purge
                'app'       => true,
            ),
            array(
                'title'     => 'Recherche développpeur Symfony2 (purge => 1 jour)',
                'user'    => $this->getReference('user1'),
                'content'   => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
                'date'      => new \DateTime(),
                'updatedAt' => (new \DateTime())->sub(new \DateInterval('P1D')), // 1 day
                'app'       => false,
            ),
            array(
                'title'     => 'Mission de webmaster 2 (purge => 1 mois',
                'user'    => $this->getReference('user1'),
                'content'   => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'      => new \DateTime(),
                'updatedAt' => (new \DateTime())->sub(new \DateInterval('P1M')), // 1 month
                'app'       => false,
            ),
            array(
                'title'     => 'Offre de stage webdesigner (purge => 14 jours)',
                'user'    => $this->getReference('user1'),
                'content'   => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'      => (new \DateTime())->sub(new \DateInterval('P14D')), // 14 days
                'updatedAt' => null,
                'app'       => false,
            ),
            array(
                'title'     => 'Recherche développpeur Symfony2 (purge => 15 jours)',
                'user'    => $this->getReference('user1'),
                'content'   => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
                'date'      => new \DateTime(),
                'updatedAt' => (new \DateTime())->sub(new \DateInterval('P15D')), // 15 days
                'app'       => false,
            ),
        );

//        Images
        $images = array();
        for($i = 1; $i <= 7; $images[] = $this->getReference('image'.$i++)) {}

//        Categories
        $categories = array();
        for($i = 1; $i <= 2; $categories[] = $this->getReference('category'.$i++)) {}

//        Skills
        $skills = array();
        for($i = 1; $i <= 7; $skills[] = $this->getReference('skill'.$i++)) {}

//        Applications
        $applications = array(
            array(
                'user'    => $this->getReference('user2'),
                'content'   => "J'ai toutes les qualités requises.",
            ),
            array(
                'user'    => $this->getReference('user2'),
                'content'   => "Je suis très motivé.",
            ),
        );

//        Images array index
        $i = 0;

        foreach($adverts as $advert) {
            $newAdvert = new Advert();
            $newAdvert->setTitle($advert['title']);
            $newAdvert->setUser($advert['user']);
            $newAdvert->setContent($advert['content']);
//            $newAdvert->setDate(new \DateTime());
            $newAdvert->setDate($advert['date']);
            $newAdvert->setUpdatedAt($advert['updatedAt']);

//            Image
            $newAdvert->setImage($images[$i++]);

//            Categories
            $newAdvert->addCategory($categories[0]);
            $newAdvert->addCategory($categories[1]);

//            Applications
//            pour tester la purge, certaines annonces ne doivent pas avoir d'applications liées
            if($advert['app'] === true) {
                foreach($applications as $application) {
                    $newApplication = new Application();
                    $newApplication->setUser($application['user']);
                    $newApplication->setContent($application['content']);
                    $newApplication->setDate(new \DateTime());

                    $newApplication->setAdvert($newAdvert);

                    $manager->persist($newApplication);
                }
            }

//            Skills
            foreach($skills as $skill) {
                $advertSkill = new AdvertSkill();
                $advertSkill->setSkill($skill);
                $advertSkill->setLevel('Expert');
//                $advertSkill->setAdvert($newAdvert);

                $newAdvert->addAdvertSkill($advertSkill); // Relation bidirectionnelle

//                $manager->persist($advertSkill);
            }

            $manager->persist($newAdvert);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}