<?php

namespace App\Controller;

use App\DTO\CreateProductDto;
use App\DTO\Query\CommonRequestQuery;
use App\Services\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

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

    #[Route('/product', name: 'app_fetch_products', methods: ['GET'])]
    public function index(#[MapQueryString] ?CommonRequestQuery $query): JsonResponse
    {
        $data = $this->productService->index($query);

        return $this->json([
            'data' => $data,
        ], 200);
    }
}
