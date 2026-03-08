<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new Product();
    }

    /**
     * Tìm sản phẩm theo tên
     */
    public function findByName(string $name): ?Product
    {
        return Product::where('name', 'like', "%{$name}%")->first();
    }

    /**
     * Lấy sản phẩm theo khoảng giá
     */
    public function getByPriceRange(float $minPrice, float $maxPrice)
    {
        return Product::whereBetween('price', [$minPrice, $maxPrice])->get();
    }
}
