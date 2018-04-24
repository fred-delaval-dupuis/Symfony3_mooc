<?php

namespace OC\UserBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUser extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function load(ObjectManager $manager)
    {
        $users = array(
            array(
                'username'  => 'fred',
                'email'     => 'fred@example.com',
                'password'  => 'fred',
            ),
            array(
                'username'  => 'john',
                'email'     => 'john@example.com',
                'password'  => 'john',
            ),
            array(
                'username'  => 'emilia',
                'email'     => 'emilia@example.com',
                'password'  => 'emilia',
            ),
        );

        $i = 1;

        foreach($users as $user) {
            $userManager = $this->container->get('fos_user.user_manager');
            $u = $userManager->createUser();
            $u->setUsername($user['username']);
            $u->setEmail($user['email']);
            $u->setPlainPassword($user['password']);

            $userManager->updateUser($u, true);

            $this->addReference('user'.$i++, $u);
        }
    }

    public function getOrder()
    {
        return 4;
    }


}