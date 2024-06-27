<?php

namespace App\Controller;

use App\DTO\CreateProductDto;
use App\Entity\Product;
use App\Services\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/product', name: 'app_create_product', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateProductDto $createProductDto,
    ): JsonResponse
    {
        $data = $this->productService->create($createProductDto);

        return $this->json([
            'message' => "Product created successfully",
            'data' => json_decode($data),
        ], 201);
    }
}
