<?php

namespace App\Services\Order;

use App\DTOs\OrderCreateDTO;
use App\DTOs\OrderItemDTO;
use App\Enums\OrderStatus;
use App\Exceptions\CustomerNotFoundException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidOrderDataException;
use App\Exceptions\ProductNotFoundException;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Exception;

class OrderCreationService
{
    /**
     * @param OrderCreateDTO $data
     * @return Order
     * @throws CustomerNotFoundException
     * @throws InsufficientStockException
     * @throws InvalidOrderDataException
     * @throws ProductNotFoundException
     */
    public function create(OrderCreateDTO $data): Order
    {

        // 1. Проверка существования клиента
        $customer = Customer::find($data->customer_id);
        if (!$customer) {
            throw new CustomerNotFoundException("Customer #{$data->customer_id} not found.");
        }

        // 2. Валидация положительности количества
        foreach ($data->items as $item) {
            if ($item->quantity <= 0) {
                throw new InvalidOrderDataException("Quantity must be positive for product #{$item->product_id}.");
            }
        }

        // 3. Загрузка товаров с пессимистической блокировкой
        $products = $this->loadProductsWithLock($data);

        // 4. Проверка существования товаров и достаточности остатков
        $this->ensureProductsExistAndStockSufficient($data, $products);

        // 5. Транзакционное создание заказа
        return DB::transaction(function () use ($data, $products, $customer) {
            $order = Order::create([
                'customer_id' => $customer->id,
                'status'      => OrderStatus::NEW->value,
                'total_amount'=> 0,
            ]);

            $totalAmount = 0.0;

            foreach ($data->items as $itemDto) {
                /** @var OrderItemDTO $itemDto */
                $product = $products[$itemDto->product_id];

                // Атомарное списание остатка с проверкой условия
                $affected = Product::where('id', $product->id)
                    ->where('stock_quantity', '>=', $itemDto->quantity)
                    ->update(['stock_quantity' => DB::raw("stock_quantity - {$itemDto->quantity}")]);

                if ($affected === 0) {
                    throw new InsufficientStockException(
                        "Insufficient stock for product #{$product->id}. Available: {$product->stock_quantity}, Requested: {$itemDto->quantity}"
                    );
                }

                // Фиксируем цену из загруженной модели
                $unitPrice = $product->price;
                $itemTotal = round($itemDto->quantity * $unitPrice, 2);

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $itemDto->quantity,
                    'unit_price' => $unitPrice,
                    'total_price'=> $itemTotal,
                ]);

                $totalAmount += $itemTotal;
            }

            // Обновляем итоговую сумму заказа
            $order->update(['total_amount' => round($totalAmount, 2)]);

            // Подгружаем связи для ответа
            $order->load(['items.product', 'customer']);

            return $order;
        });
    }

    /**
     * @throws Exception
     */


    /**
     * Загружает все товары, участвующие в заказе, с пессимистической блокировкой.
     *
     * @param OrderCreateDTO $data
     * @return Collection Коллекция моделей Product с ключами по id
     */
    private function loadProductsWithLock(OrderCreateDTO $data): Collection
    {
        $productIds = $data->items
            ->toCollection()
            ->pluck('product_id')
            ->unique()
            ->toArray();

        return Product::whereIn('id', $productIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    /**
     * Проверяет существование всех запрошенных товаров и достаточность остатков.
     *
     * @param OrderCreateDTO $data
     * @param Collection $products
     *
     * @throws ProductNotFoundException
     * @throws InsufficientStockException
     */
    private function ensureProductsExistAndStockSufficient(OrderCreateDTO $data, Collection $products): void
    {
        /** @var OrderItemDTO $itemDto */
        foreach ($data->items as $itemDto) {
            if (!$products->has($itemDto->product_id)) {
                throw new ProductNotFoundException("Product #{$itemDto->product_id} not found.");
            }

            $product = $products[$itemDto->product_id];
            if ($product->stock_quantity < $itemDto->quantity) {
                throw new InsufficientStockException(
                    "Insufficient stock for product '{$product->name}'. Available: {$product->stock_quantity}, Requested: {$itemDto->quantity}"
                );
            }
        }
    }

}