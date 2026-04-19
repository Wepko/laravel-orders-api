<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\ProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use BaseApiController;
use Illuminate\Http\JsonResponse;
//use Spatie\ResponseCache\Attributes\Cache;

class ProductController extends BaseApiController
{
    public function __construct(
        private readonly ProductService $service
    ) {}

    //#[Cache(lifetime: 60)]
    public function index(ProductIndexRequest $request): JsonResponse
    {
        $productsPaginator = $this->service->getPaginateProducts(
            filters: $request->toDTO()
        );

        return response()->json(ProductResource::collection($productsPaginator));
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(ProductDTO::from($product));
    }
}
