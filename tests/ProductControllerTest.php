<?php

namespace App\Tests;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->token = $this->getToken();
    }

    protected function getToken(): string
    {
        $data = [
            'username' => 'test@example.com',
            'password' => 'test_password',
        ];

        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        return $responseContent['token'];
    }

    public function getProductId(): ?int
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $productRepository = $entityManager->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Test Product']);
        return $product->getId();
    }

    public function testCreateProductSuccessfully(): void
    {
        $data = [
            'name' => 'Test Product',
            'price' => 100,
            'quantity' => 10,
            'description' => 'A test product'
        ];

        $this->client->request(
            'POST',
            '/api/product',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode($data)
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Product created successfully', $responseContent['message']);
        $this->assertArrayHasKey('data', $responseContent);
    }

    public function testFetchProducts(): void
    {
        $this->client->request(
            'GET',
            '/api/product',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $responseContent);
    }

    public function testFindSpecificProduct(): void
    {
        $this->client->request(
            'GET',
            '/api/product/' . $this->getProductId(),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $responseContent);
    }

    public function testUpdateProductSuccessfully(): void
    {
        $data = [
            'name' => 'Updated Product',
            'price' => 150,
            'quantity' => 20,
            'description' => 'An updated test product'
        ];

        $this->client->request(
            'PUT',
            '/api/product/' . $this->getProductId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode($data)
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Product updated successfully', $responseContent['message']);
        $this->assertArrayHasKey('data', $responseContent);
    }

    public function testDeleteProductSuccessfully(): void
    {
        $this->client->request(
            'DELETE',
            '/api/product/' . $this->getProductId(),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Product deleted successfully', $responseContent['message']);
    }
}
