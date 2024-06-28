<?php

namespace App\Tests;

use App\DTO\RegisterUserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthServiceTest extends KernelTestCase
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $entityManager;
    private User $user;
    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->user = $this->createMock(User::class);

        $this->authService = new AuthService(
            $this->user,
            $this->entityManager,
            $this->hasher,
        );
    
        $this->createMock(UserRepository::class);
    }

    public function testCreateUserSuccessfully(): void
    {
        $dto = new RegisterUserDto('test@example.com', 'test_password');
        $hashedPassword = '$2y$13$9K3JKc7ukpA9rIeC9C5QxOb4Zr2/IOlgGKg7s9n3BMpxbVLy1hcyW';

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn($hashedPassword);

        $this->authService->create($dto);

        $this->entityManager->flush();
    }
}
