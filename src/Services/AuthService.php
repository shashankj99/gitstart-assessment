<?php

namespace App\Services;

use App\DTO\RegisterUserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    private User $user;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher,
    )
    {
        $this->user = $user;
        $this->entityManager = $entityManager;
        $this->hasher = $hasher;
    }

    public function create(RegisterUserDto $dto): void
    {
        $this->user->setEmail($dto->email);
        $this->user->setPassword(
            $this->hasher->hashPassword(
                $this->user,
                $dto->password,
            ),
        );

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }
}
