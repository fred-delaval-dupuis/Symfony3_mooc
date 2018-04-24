<?php

namespace OC\PlatformBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadImage extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $nbImages = 7;

        $path = __DIR__.'/../../../../../dist/images/';
        $filename = 'job-de-reve.jpg';
        $name = 'job de reve';
        $size = filesize($path.$filename);

        for($i = 1; $i <= $nbImages; $i++) {

//            Copie de l'image de base
            $newPath = tempnam(sys_get_temp_dir(), 'upl');
            copy($path.$filename, $newPath);

            $uploadedFile = new UploadedFile($newPath, $name, 'image/jpg', $size, UPLOAD_ERR_OK, true);

            $newImage = new Image();
            $newImage->setFile($uploadedFile);

            $manager->persist($newImage);
            $this->addReference('image'.$i, $newImage);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}