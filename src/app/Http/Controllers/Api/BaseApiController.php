<?php

declare(strict_types=1);

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Builder;
class BaseApiController extends Controller
{
    /**
     * Универсальный ответ
     *
     * Примеры:
     * $this->json($data)                          // успех 200
     * $this->json($data, 'message', 201)          // создано
     * $this->json($query, 'message', 200, ['resource' => ProductResource::class]) // пагинация
     * $this->json(null, 'deleted', 204)           // удалено без контента
     * $this->jsonError('error', 'CODE', 400)      // ошибка
     */
    protected function json(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = 200,
        array $options = []
    ): JsonResponse {
        if ($data instanceof Builder) {
            return $this->jsonPaginate($data, $message, $options);
        }

        // No Content (204)
        if ($statusCode === 204 || ($data === null && $statusCode === 200 && $message)) {
            return response()->json([
                'success' => true,
                'data' => null,
                'meta' => null,
                'error' => null,
                'message' => $message,
            ], $statusCode === 200 ? 204 : $statusCode);
        }

        return response()->json([
            'success' => true,
            'data' => $data instanceof JsonResource ? $data : ($data ?? null),
            'meta' => $options['meta'] ?? null,
            'error' => null,
            'message' => $message,
        ], $statusCode);
    }

    private function jsonPaginate(Builder $query, ?string $message = null, array $options = []): JsonResponse
    {
        $limit = min((int) request()->get('limit', $options['default_limit'] ?? 15), $options['max_limit'] ?? 100);
        $offset = (int) request()->get('offset', 0);

        $total = $query->count();
        $items = $query->limit($limit)->offset($offset)->get();

        $resourceClass = $options['resource'] ?? null;
        $data = $resourceClass ? $resourceClass::collection($items) : $items;

        $meta = [
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
                'total' => $total,
                'current_count' => $items->count(),
                'has_more' => ($offset + $limit) < $total,
            ],
        ];

        if ($options['filters'] ?? false) {
            $meta['filters'] = $options['filters'];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => $meta,
            'error' => null,
            'message' => $message,
        ], 200);
    }

    protected function jsonError(
        string $message,
        string $errorCode = 'GENERAL_ERROR',
        int $statusCode = 400,
        ?array $details = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'data' => null,
            'meta' => null,
            'error' => [
                'code' => $errorCode,
                'message' => $message,
                'details' => $details,
            ],
            'message' => $message,
        ], $statusCode);
    }

    // Удобные сокращения для частых случаев
    protected function jsonCreated(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return $this->json($data, $message, 201);
    }

    protected function jsonUpdated(mixed $data = null, string $message = 'Updated'): JsonResponse
    {
        return $this->json($data, $message);
    }

    protected function jsonDeleted(string $message = 'Deleted'): JsonResponse
    {
        return $this->json(null, $message, 204);
    }

    protected function jsonNotFound(string $resource, $id = null): JsonResponse
    {
        return $this->jsonError("{$resource} not found", 'NOT_FOUND', 404, $id ? [$resource => $id] : null);
    }

    protected function jsonValidationError(?array $errors = null): JsonResponse
    {
        return $this->jsonError('Validation failed', 'VALIDATION_ERROR', 422, $errors);
    }

    protected function jsonRateLimit(int $retryAfter = 60): JsonResponse
    {
        return $this->jsonError('Too many requests', 'RATE_LIMIT_EXCEEDED', 429, ['retry_after' => $retryAfter]);
    }
}
