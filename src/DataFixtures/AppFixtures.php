<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture 
{

    public $faker;
    public $hasher;
    public $entitymanager;

    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $entitymanager) {
        $this->faker = Factory::create();
        $this->hasher = $hasher;
        $this->entitymanager = $entitymanager;
    }

    public function load(ObjectManager $manager): void {

        $person = new Person();
        $person->setAge('23');
        $person->setGender('Male');
        $person->setName('Indeche Evans');
        $person->setNationalId('1203939');
        $person->setPhonenumber('0720389023');
        $person->setSecondaryphone('073047886');
        $person->setUsername('obrien');
        $manager->persist($person);
        $manager->flush();

        $admin_user = new User();
        $admin_user->setEmail('i.ojukwu.e@gmail.com');
        $password = $this->hasher->hashPassword($admin_user, 'zeus');
        $admin_user->setPassword($password);
        $admin_user->setPerson($person);
        $admin_user->setRoles(['ROLE_SUPER_ADMIN','ROLE_ADMIN']);
        $manager->persist($admin_user);

        $person = new Person();
        $person->setAge('23');
        $person->setGender('Male');
        $person->setName('Winston Wacieni');
        $person->setNationalId('1203939');
        $person->setPhonenumber('0720379023');
        $person->setSecondaryphone('073907686');
        $person->setUsername('winston');
        $manager->persist($person);
        $manager->flush();

        $admin_user = new User();
        $admin_user->setEmail('cloud.wacieni@gmail.com');
        $password = $this->hasher->hashPassword($admin_user, 'poseidon');
        $admin_user->setPassword($password);
        $admin_user->setPerson($person);
        $admin_user->setRoles(['ROLE_SUPER_ADMIN','ROLE_ADMIN']);
        $manager->persist($admin_user);
        $manager->flush();

    }
}
