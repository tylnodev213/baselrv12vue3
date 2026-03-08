<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Repositories\ProductRepository;

class ProductService extends BaseService
{
    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->repository = $productRepository;
    }

    protected function getRepository(): BaseRepository
    {
        return $this->productRepository;
    }

    /**
     * Tìm sản phẩm theo tên
     */
    public function findByName(string $name)
    {
        return $this->productRepository->findByName($name);
    }

    /**
     * Lấy sản phẩm theo khoảng giá
     */
    public function getByPriceRange(float $minPrice, float $maxPrice)
    {
        return $this->productRepository->getByPriceRange($minPrice, $maxPrice);
    }
}
