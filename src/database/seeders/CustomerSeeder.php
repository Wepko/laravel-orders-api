<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Ivan Petrov', 'email' => 'ivan.petrov@example.com', 'phone' => '+7-999-123-4567'],
            ['name' => 'Anna Smirnova', 'email' => 'anna.smirnova@example.com', 'phone' => '+7-999-234-5678'],
            ['name' => 'Dmitry Kozlov', 'email' => 'dmitry.kozlov@example.com', 'phone' => '+7-999-345-6789'],
            ['name' => 'Elena Volkov', 'email' => 'elena.volkov@example.com', 'phone' => '+7-999-456-7890'],
            ['name' => 'Sergey Nikitin', 'email' => 'sergey.nikitin@example.com', 'phone' => '+7-999-567-8901'],
            ['name' => 'Maria Fedorova', 'email' => 'maria.fedorova@example.com', 'phone' => '+7-999-678-9012'],
            ['name' => 'Alexey Orlov', 'email' => 'alexey.orlov@example.com', 'phone' => '+7-999-789-0123'],
            ['name' => 'Natalia Sergeeva', 'email' => 'natalia.sergeeva@example.com', 'phone' => '+7-999-890-1234'],
            ['name' => 'Vladimir Lebedev', 'email' => 'vladimir.lebedev@example.com', 'phone' => '+7-999-901-2345'],
            ['name' => 'Ekaterina Morozova', 'email' => 'ekaterina.morozova@example.com', 'phone' => '+7-999-012-3456'],
        ];

        foreach ($customers as $customer) {
            Customer::factory()->create($customer);
        }
    }
}
