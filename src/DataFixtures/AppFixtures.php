<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $basicUser = new User;
        $manager->persist($basicUser);
        $username = 'admin';
        $basicUser->setUsername($username);
        $basicUser->setEmail('admin@colorize.com');
        $hashedPassword = $this->passwordHasher->hashPassword($basicUser, $username);
        $basicUser->setPassword($hashedPassword);
            
        $manager->flush();
    }
}