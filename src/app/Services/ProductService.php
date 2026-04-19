<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductFilterDTO;
use App\Filters\ProductFilter;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        protected  ProductFilter $filter
    ) {}

    /**
     * Get paginated list of products with filters.
     */
    public function getPaginateProducts(ProductFilterDTO $productFilterDTO): LengthAwarePaginator
    {
        $query = $this->buildQuery($productFilterDTO);

        return $query->paginate(
            $productFilterDTO->perPage,
            ['*'],
            'page',
            $productFilterDTO->page
        );
    }

    private function buildQuery(ProductFilterDTO $productFilterDTO)
    {
        $query = Product::query()->with('category');

        // Применяем фильтры через класс ProductFilter
        $this->filter->apply($query, [
            'inStock' => $productFilterDTO->inStock,
            'priceFrom' => $productFilterDTO->priceFrom,
            'priceTo' => $productFilterDTO->priceTo,
            'categoryId' => $productFilterDTO->categoryId,
            'ratingFrom' => $productFilterDTO->ratingFrom,
            'q' => $productFilterDTO->q,
        ]);

        // Сортировка
        return $query;
    }

    /**
     * Get single product by ID.
     */
    public function getProduct(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Get product by ID or fail.
     */
    public function getProductOrFail(int $id): Product
    {
        return Product::findOrFail($id);
    }

    /**
     * Check if product has sufficient stock.
     */
    public function hasStock(Product $product, int $quantity): bool
    {
        return $product->stock_quantity >= $quantity;
    }

    /**
     * Reduce product stock.
     */
    public function reduceStock(Product $product, int $quantity): bool
    {
        if (!$this->hasStock($product, $quantity)) {
            return false;
        }

        $product->decrement('stock_quantity', $quantity);
        return true;
    }

    /**
     * Increase product stock (for cancelled orders).
     */
    public function increaseStock(Product $product, int $quantity): void
    {
        $product->increment('stock_quantity', $quantity);
    }

    /**
     * Validate stock for multiple products.
     *
     * @throws \RuntimeException
     */
    public function validateStock(array $items): void
    {
        $errors = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                $errors[] = "Product #{$item['product_id']} not found";
                continue;
            }

            if (!$this->hasStock($product, $item['quantity'])) {
                $errors[] = "Insufficient stock for '{$product->name}'. Available: {$product->stock_quantity}, Requested: {$item['quantity']}";
            }
        }

        if (!empty($errors)) {
            throw new \RuntimeException(implode('; ', $errors));
        }
    }
}
