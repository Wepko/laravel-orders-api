<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DTOs\ProductDTO;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $this->resource;

        $dto = new ProductDTO(
            id: $product->id,
            name: $product->name,
            sku: $product->sku,
            price: (float) $product->price,
            stock_quantity: $product->stock_quantity,
            category: $product->category,
            created_at: $product->created_at,
            updated_at: $product->updated_at,
        );

        return $dto->toArray();
    }
}
