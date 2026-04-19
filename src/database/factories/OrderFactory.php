<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'status' => OrderStatus::NEW->value,
            'total_amount' => 0,
            'confirmed_at' => null,
            'shipped_at' => null,
        ];
    }

    /**
     * Set specific status.
     */
    public function status(string $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
            'confirmed_at' => in_array($status, [
                OrderStatus::CONFIRMED->value,
                OrderStatus::PROCESSING->value,
                OrderStatus::SHIPPED->value,
                OrderStatus::COMPLETED->value,
            ]) ? now() : null,
            'shipped_at' => in_array($status, [
                OrderStatus::SHIPPED->value,
                OrderStatus::COMPLETED->value,
            ]) ? now() : null,
        ]);
    }

    /**
     * Set confirmed status.
     */
    public function confirmed(): static
    {
        return $this->status(OrderStatus::CONFIRMED->value);
    }

    /**
     * Set shipped status.
     */
    public function shipped(): static
    {
        return $this->status(OrderStatus::SHIPPED->value);
    }

    /**
     * Set cancelled status.
     */
    public function cancelled(): static
    {
        return $this->status(OrderStatus::CANCELLED->value);
    }
}