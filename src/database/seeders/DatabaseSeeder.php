<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
            CustomerSeeder::class,
        ]);

        $customers = Customer::all();
        $products = Product::all();

        if ($customers->isNotEmpty() && $products->isNotEmpty()) {
            $statuses = [
                OrderStatus::NEW,
                OrderStatus::NEW,
                OrderStatus::CONFIRMED,
                OrderStatus::PROCESSING,
                OrderStatus::SHIPPED,
            ];

            foreach ($statuses as $status) {
                $order = Order::factory()->create([
                    'customer_id' => $customers->random()->id,
                    'status' => $status->value,
                    'confirmed_at' => in_array($status, [OrderStatus::CONFIRMED, OrderStatus::PROCESSING, OrderStatus::SHIPPED])
                        ? now()->subDays(rand(1, 5))
                        : null,
                    'shipped_at' => in_array($status, [OrderStatus::SHIPPED])
                        ? now()->subDay()
                        : null,
                ]);

                // Add 1-4 items to each order
                $itemCount = rand(1, 4);
                $selectedProducts = $products->random($itemCount);
                $totalAmount = 0;

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    $unitPrice = (float) $product->price;
                    $totalPrice = round($quantity * $unitPrice, 2);
                    $totalAmount += $totalPrice;

                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ]);
                }

                $order->update(['total_amount' => $totalAmount]);
            }
        }
    }
}
