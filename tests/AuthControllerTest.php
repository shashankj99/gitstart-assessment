<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRegisterUserSuccessfully(): void
    {
        $client = static::createClient();

        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'test@example.com']);

        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

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
