<?php

namespace App\Controller;

use App\DTO\CreateProductDto;
use App\DTO\Query\CommonRequestQuery;
use App\DTO\UpdateProductDto;
use App\Services\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/product', name: 'create_product', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateProductDto $createProductDto,
    ): JsonResponse {
        $data = $this->productService->create($createProductDto);

        return $this->json([
            'message' => "Product created successfully",
            'data' => json_decode($data),
        ], 201);
    }

    #[Route('/product', name: 'fetch_products', methods: ['GET'])]
    public function index(#[MapQueryString] ?CommonRequestQuery $query): JsonResponse
    {
        $data = $this->productService->index($query);

        return $this->json([
            'data' => $data,
        ], 200);
    }

    #[Route('/product/{id}', name: 'find_specific_product', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $data = $this->productService->show($id);

        return $this->json([
            'data' => $data,
        ], 200);
    }

    #[Route('/product/{id}', name: 'update_specific_product', methods: ['PUT'])]
    public function update(
        #[MapRequestPayload] UpdateProductDto $dto,
        int $id,
    ): JsonResponse {
        $data = $this->productService->update($dto, $id);

        return $this->json([
            'message' => 'Product updated successfully',
            'data' => json_decode($data),
        ], 200);
    }

    #[Route('/product/{id}', name: 'delete_specific_product', methods: ['DELETE'])]
    public function delete(
        int $id,
    ): JsonResponse {
        $this->productService->remove($id);

        return $this->json([
            'message' => 'Product deleted successfully',
        ], 200);
    }
}
