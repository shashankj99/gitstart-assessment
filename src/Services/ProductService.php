<?php

namespace App\Services;

use App\DTO\CreateProductDto;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProductService
{
    private Product $product;
    private EntityManagerInterface $entityManagerInterface;
    private SerializerInterface $serializer;

    public function __construct(
        Product $product,
        EntityManagerInterface $entityManagerInterface,
        SerializerInterface $serializer,
    )
    {
        $this->product = $product;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->serializer = $serializer;
    }

    public function create(CreateProductDto $createProductDto): string
    {
        $this->product->setName($createProductDto->name);
        $this->product->setPrice($createProductDto->price);
        $this->product->setQuantity($createProductDto->quantity);

        if ($createProductDto->description) {
            $this->product->setDescription($createProductDto->description);
        }

        $this->entityManagerInterface->persist($this->product);
        $this->entityManagerInterface->flush();

        return $this->serializer->serialize($this->product, 'json');
    }
}