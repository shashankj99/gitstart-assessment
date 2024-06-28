<?php

namespace App\Tests;

use App\DTO\CreateProductDto;
use App\DTO\Query\CommonRequestQuery;
use App\DTO\UpdateProductDto;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Services\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ProductServiceTest extends KernelTestCase
{
    private Product $product;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ProductRepository $productRepository;
    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->product = $this->createMock(Product::class);
        $this->productRepository = $this->createMock(ProductRepository::class);

        $this->service = new ProductService(
            $this->product,
            $this->productRepository,
            $this->entityManager,
            $this->serializer,
        );
    }

    public function testCreateProductSuccessfully(): void
    {
        $dto = new CreateProductDto('test product', 10, 15, '');

        $this->product->expects($this->once())
            ->method('setName')
            ->with('test product')
            ->willReturnSelf();

        $this->product->expects($this->once())
            ->method('setPrice')
            ->with(10)
            ->willReturnSelf();

        $this->product->expects($this->once())
            ->method('setQuantity')
            ->with(15)
            ->willReturnSelf();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->product);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($this->product, 'json')
            ->willReturn('{"name":"test product","price":10,"quantity":15,"description":""}');

        $result = $this->service->create($dto);

        $this->assertEquals(
            '{"name":"test product","price":10,"quantity":15,"description":""}',
            $result,
        );
    }

    public function testFetchProductSuccessfully(): void
    {
        $query = new CommonRequestQuery(1, 10, 'desc');

        $expectedResult = [
            ['name' => 'test product 1', 'price' => 10, 'quantity' => 15],
            ['name' => 'test product 2', 'price' => 20, 'quantity' => 25],
        ];

        $this->productRepository->expects($this->once())
            ->method('fetchPaginatedProduct')
            ->with($query->page, $query->limit, $query->order, $query->search)
            ->willReturn($expectedResult);

        $result = $this->service->index($query);

        $this->assertEquals($expectedResult, $result);
    }

    public function testShowProductSuccessfully(): void
    {
        $productId = 1;
        $expectedProduct = $this->createMock(Product::class);

        $this->productRepository->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn($expectedProduct);

        $result = $this->service->show($productId);

        $this->assertSame($expectedProduct, $result);
    }

    public function testShowProductNotFound(): void
    {
        $productId = 1;

        $this->productRepository->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage("Unable to find the product with id $productId");

        $this->service->show($productId);
    }

    public function testUpdateProductSuccessfully(): void
    {
        $productId = 1;
        $dto = new UpdateProductDto('updated product', 20, 30, 'updated description');
        $existingProduct = $this->createMock(Product::class);

        $this->productRepository->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn($existingProduct);

        $existingProduct->expects($this->once())
            ->method('setName')
            ->with('updated product')
            ->willReturnSelf();

        $existingProduct->expects($this->once())
            ->method('setPrice')
            ->with(20)
            ->willReturnSelf();

        $existingProduct->expects($this->once())
            ->method('setQuantity')
            ->with(30)
            ->willReturnSelf();

        $existingProduct->expects($this->once())
            ->method('setDescription')
            ->with('updated description')
            ->willReturnSelf();

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($existingProduct, 'json')
            ->willReturn('{"name":"updated product","price":20,"quantity":30,"description":"updated description"}');

        $result = $this->service->update($dto, $productId);

        $this->assertEquals(
            '{"name":"updated product","price":20,"quantity":30,"description":"updated description"}',
            $result,
        );
    }

    public function testUpdateProductNotFound(): void
    {
        $productId = 1;
        $dto = new UpdateProductDto('updated product', 20, 30, 'updated description');

        $this->productRepository->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage("Unable to find the product with id $productId");

        $this->service->update($dto, $productId);
    }

    public function testRemoveProductSuccessfully(): void
    {
        $productId = 1;
        $existingProduct = $this->createMock(Product::class);

        $this->productRepository->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn($existingProduct);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($existingProduct);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->service->remove($productId);
    }

    public function testRemoveProductNotFound(): void
    {
        $productId = 1;

        $this->productRepository->expects($this->once())
            ->method('find')
            ->with($productId)
            ->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage("Unable to find the product with id $productId");

        $this->service->remove($productId);
    }
}
