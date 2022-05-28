<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->encoder = $encoder;
        $this->em = $entityManager;
    }

    public function load(\Doctrine\Persistence\ObjectManager $manager)
    {
        $usersData = [
            0 => [
                'email' => 'user@example.com',
                'role' => [],
                'password' => 123654,
                'numberPhone' => '89049804178',
                'birthdate' => (new \DateTime("23.08.1999")),
                'surname' => 'Осипов',
                'name' => 'Артём',
                'patronymic' => 'Александрович'
            ]
        ];

        foreach ($usersData as $user) {
            $newUser = new User();
            $newUser->setEmail($user['email']);
            $newUser->setPassword($this->encoder->encodePassword($newUser, $user['password']));
            $newUser->setRoles($user['role']);
            $newUser->setNumberPhone($user['numberPhone']);
            $newUser->setBirthdate($user['birthdate']);
            $newUser->setSurname($user['surname']);
            $newUser->setName($user['name']);
            $newUser->setPatronymic($user['patronymic']);
            $this->em->persist($newUser);
        }

        $this->em->flush();
    }
}