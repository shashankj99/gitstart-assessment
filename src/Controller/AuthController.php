<?php

namespace App\Controller;

use App\DTO\RegisterUserDto;
use App\Services\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[Route('/register', name: 'app_auth')]
    public function register(
        #[MapRequestPayload] RegisterUserDto $dto,
    ): JsonResponse {
        $this->authService->create($dto);

        return $this->json([
            'message' => 'Registration Successful!',
        ], 201);
    }
}
