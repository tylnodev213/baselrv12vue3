<?php

namespace App\Http\Controllers;

use App\Services\BaseService;
use App\Services\ProductService;

class ProductController extends BaseController
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->service = $productService;
    }

    protected function getService(): BaseService
    {
        return $this->productService;
    }
}
