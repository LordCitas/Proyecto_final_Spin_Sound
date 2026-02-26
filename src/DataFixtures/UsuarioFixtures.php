<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsuarioFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Crear 2 usuarios normales
        $usuario1 = new Usuario();
        $usuario1->setEmail('usuario1@test.com');
        $usuario1->setNombre('Usuario Uno');
        $usuario1->setDireccion('Calle Usuario 1, Madrid');
        $usuario1->setTelefono(600111111);
        $usuario1->setRoles(['ROLE_USER']);
        $usuario1->setPassword($this->passwordHasher->hashPassword($usuario1, 'password123'));
        $usuario1->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($usuario1);

        $usuario2 = new Usuario();
        $usuario2->setEmail('usuario2@test.com');
        $usuario2->setNombre('Usuario Dos');
        $usuario2->setDireccion('Calle Usuario 2, Barcelona');
        $usuario2->setTelefono(600222222);
        $usuario2->setRoles(['ROLE_USER']);
        $usuario2->setPassword($this->passwordHasher->hashPassword($usuario2, 'password123'));
        $usuario2->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($usuario2);

        // Crear 2 administradores
        $admin1 = new Admin();
        $admin1->setEmail('admin1@test.com');
        $admin1->setNombre('Admin Uno');
        $admin1->setDireccion('Calle Admin 1, Madrid');
        $admin1->setTelefono(600333333);
        $admin1->setRoles(['ROLE_ADMIN']);
        $admin1->setPassword($this->passwordHasher->hashPassword($admin1, 'admin123'));
        $admin1->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($admin1);

        $admin2 = new Admin();
        $admin2->setEmail('admin2@test.com');
        $admin2->setNombre('Admin Dos');
        $admin2->setDireccion('Calle Admin 2, Valencia');
        $admin2->setTelefono(600444444);
        $admin2->setRoles(['ROLE_ADMIN']);
        $admin2->setPassword($this->passwordHasher->hashPassword($admin2, 'admin123'));
        $admin2->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($admin2);

        // Crear 1 super administrador
        $superAdmin = new Admin();
        $superAdmin->setEmail('superadmin@test.com');
        $superAdmin->setNombre('Super Admin');
        $superAdmin->setDireccion('Calle Super Admin, Madrid');
        $superAdmin->setTelefono(600555555);
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']);
        $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'superadmin123'));
        $superAdmin->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($superAdmin);

        $manager->flush();
    }
}
