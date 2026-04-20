<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTOs\ProductDTO;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Spatie\ResponseCache\Attributes\Cache;

#[OA\Get(
    path: '/api/v1/products',
    operationId: 'getProducts',
    summary: 'Получить список товаров с фильтрацией и поиском',
    tags: ['Products'],
    parameters: [
        new OA\QueryParameter(name: 'q', description: 'Поиск по названию или SKU', required: false, schema: new OA\Schema(type: 'string')),
        new OA\QueryParameter(name: 'category', description: 'Фильтр по категории', required: false, schema: new OA\Schema(type: 'string')),
        new OA\QueryParameter(name: 'limit', description: 'Количество товаров на странице', required: false, schema: new OA\Schema(type: 'integer', default: 15, minimum: 1, maximum: 100)),
        new OA\QueryParameter(name: 'cursor', description: 'Курсор для пагинации', required: false, schema: new OA\Schema(type: 'string')),
    ]
)]
#[OA\Response(
    response: 200,
    description: 'Успешный ответ со списком товаров'
)]
class ProductController extends BaseApiController
{
    public function __construct(
        private readonly ProductService $service
    ) {}

    #[Cache(lifetime: 60)]
    public function index(ProductIndexRequest $request): JsonResponse
    {
        $productsPaginator = $this->service->getPaginationProducts(
            $request->toDTO()
        );

        $resource = ProductResource::collection($productsPaginator);

        return $this->jsonPaginate(
            data: $resource,
            paginator: $productsPaginator,
            message:  __('Products retrieved successfully'));

    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(ProductDTO::from($product));
    }
}
