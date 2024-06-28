<?php

namespace App\Tests;

use App\DTO\RegisterUserDto;
use App\Services\AuthService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = $this->createMock(AuthService::class);
    }

    public function testRegisterUserSuccessfully(): void
    {
        $dto = new RegisterUserDto('test@example.com', 'test_password');

        $this->authService->expects($this->once())
            ->method('create')
            ->with($dto);

        $client = static::createClient();
        $container = $client->getContainer();
        $container->set(AuthService::class, $this->authService);

        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'password' => 'test_password',
        ]));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Registration Successful!']),
            $client->getResponse()->getContent()
        );
    }

    public function testRegisterUserValidationErrors(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $container->set(AuthService::class, $this->authService);

        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'first_name' => 'something',
            'last_name' => 'else'
        ]));

        $this->assertEquals(422, $client->getResponse()->getStatusCode());

        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'message' => 'Unprocessable Entity',
                'errors' => [
                    [
                        'field' => 'email',
                        'message' => 'This value should be of type string.',
                    ],
                    [
                        'field' => 'password',
                        'message' => 'This value should be of type string.',
                    ],
                ],
            ]),
            $client->getResponse()->getContent()
        );
    }
}

