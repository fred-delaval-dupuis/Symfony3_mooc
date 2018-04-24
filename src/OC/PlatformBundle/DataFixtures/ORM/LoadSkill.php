<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Skill;


class LoadSkill extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
//        Liste des noms de compétences à ajouter
        $names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-note');

        $i = 1;

        foreach($names as $name) {
            $skill = new Skill();
            $skill->setName($name);
            $manager->persist($skill);

            $this->addReference('skill'.$i++, $skill);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}