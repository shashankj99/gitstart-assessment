<?php

namespace App\Services;

use App\DTO\CreateProductDto;
use App\DTO\Query\CommonRequestQuery;
use App\DTO\UpdateProductDto;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;

class ProductService
{
    private Product $product;
    private EntityManagerInterface $entityManagerInterface;
    private SerializerInterface $serializer;
    private ProductRepository $productRepository;

    public function __construct(
        Product $product,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManagerInterface,
        SerializerInterface $serializer,
    ) {
        $this->product = $product;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
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

    public function index(?CommonRequestQuery $query): array
    {
        if ($query === null) {
            $query = new CommonRequestQuery();
        }

        return $this->productRepository->fetchPaginatedProduct(
            $query->page,
            $query->limit,
            $query->order,
            $query->search,
        );
    }

    public function show(int $id): Product
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw new EntityNotFoundException("Unable to find the product with id $id");
        }

        return $product;
    }

    public function update(UpdateProductDto $dto, int $id): string
    {
        $product = $this->show($id);

        $product->setName($dto->name);
        $product->setPrice($dto->price);
        $product->setQuantity($dto->quantity);

        if ($dto->description) {
            $product->setDescription($dto->description);
        }

        $this->entityManagerInterface->flush();

        return $this->serializer->serialize($product, 'json');
    }

    public function remove(int $id): void
    {
        $product = $this->show($id);

        $this->entityManagerInterface->remove($product);
        $this->entityManagerInterface->flush();
    }
}
