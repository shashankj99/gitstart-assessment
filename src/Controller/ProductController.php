<?php

namespace App\Controller;

use App\DTO\CreateProductDto;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_create_product', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateProductDto $createProductDto,
        EntityManagerInterface $entityManagerInterface,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $product = new Product();

        $product->setName($createProductDto->name);
        $product->setPrice($createProductDto->price);
        $product->setQuantity($createProductDto->quantity);

        if ($createProductDto->description) {
            $product->setDescription($createProductDto->description);
        }

        $entityManagerInterface->persist($product);
        $entityManagerInterface->flush();

        $data = $serializer->serialize($product, 'json');

        return $this->json([
            'message' => "Product created successfully",
            'data' => json_decode($data),
        ], 201);
    }
}
