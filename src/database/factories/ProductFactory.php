<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $categories = ['Engine', 'Brakes', 'Suspension', 'Electrical', 'Body Parts', 'Interior', 'Transmission', 'Cooling'];

        return [
            'name' => fake()->unique()->randomElement([
                'Oil Filter',
                'Air Filter',
                'Brake Pads',
                'Spark Plugs',
                'Alternator',
                'Starter Motor',
                'Water Pump',
                'Timing Belt',
                'Clutch Kit',
                'Radiator',
                'Battery',
                'Shock Absorber',
                'Control Arm',
                'Drive Belt',
                'Fuel Injector',
                'Thermostat',
                'Wiper Blades',
                'Headlight Bulb',
                'Brake Rotor',
                'Ball Joint',
            ]),
            'sku' => strtoupper(fake()->unique()->bothify('??-####-??')),
            'price' => fake()->randomFloat(2, 10, 500),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'category' => fake()->randomElement($categories),
        ];
    }

    /**
     * Indicate the product is in stock.
     */
    public function inStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => fake()->numberBetween(10, 100),
        ]);
    }

    /**
     * Indicate the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }

    /**
     * Set specific category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }
}
