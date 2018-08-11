<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AuthorFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 1; $i < 5; $i++){
            $author = new Author();
            $author->setFirstName('max'.$i);
            $author->setLastName('star');
            $author->setMiddleName('yur');
            $manager->persist($author);


        }

        $manager->flush();
    }
}
