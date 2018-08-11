<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Magazine;
use App\Repository\AuthorRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MagazineFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $time = new \DateTime();
        $authorRepo = $manager->getRepository("App:Author");
        $authors = $authorRepo->findAll();
        foreach ($authors as $author) {
            for ($i = 1; $i < 3 ; $i++) {
                $magazine = new Magazine();
                $magazine->setName('Magazine #'.$i);
                $magazine->setDate($time->setDate(2018, $i, 1));
                $magazine->setDescription("Журнал");


                $magazine->addAuthor($author);
                $manager->persist($magazine);
                $manager->flush();
            }

        }

    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        // TODO: Implement getDependencies() method.
        return [
            AuthorFixtures::class
        ];
    }
}
