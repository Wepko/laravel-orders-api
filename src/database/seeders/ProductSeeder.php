<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $categories = [
            'Engine' => [
                'Oil Filter',
                'Air Filter',
                'Fuel Filter',
                'Spark Plugs',
                'Timing Belt',
                'Water Pump',
                'Thermostat',
                'Oil Pump',
                'Valve Cover Gasket',
                'Engine Mount',
            ],
            'Brakes' => [
                'Brake Pads',
                'Brake Rotor',
                'Brake Caliper',
                'Brake Lines',
                'Brake Fluid',
                'Brake Shoes',
                'Wheel Cylinder',
                'Master Cylinder',
                'Brake Booster',
                'Parking Brake Cable',
            ],
            'Suspension' => [
                'Shock Absorber',
                'Strut Assembly',
                'Control Arm',
                'Ball Joint',
                'Tie Rod End',
                'Sway Bar Link',
                'Coil Spring',
                'Leaf Spring',
                'Wheel Bearing',
                'Bushing Kit',
            ],
            'Electrical' => [
                'Alternator',
                'Starter Motor',
                'Battery',
                'Ignition Coil',
                'Crankshaft Position Sensor',
                'Oxygen Sensor',
                'Mass Air Flow Sensor',
                'Fuse Box',
                'Relay',
                'Wiring Harness',
            ],
            'Body Parts' => [
                'Front Bumper',
                'Rear Bumper',
                'Side Mirror',
                'Headlight',
                'Taillight',
                'Fender',
                'Door Panel',
                'Hood',
                'Trunk Lid',
                'Windshield',
            ],
            'Interior' => [
                'Seat Cover',
                'Floor Mats',
                'Steering Wheel Cover',
                'Gear Knob',
                'Dashboard Cover',
                'Sunshade',
                'Armrest',
                'Cargo Liner',
                'Seat Belt',
                'Interior Light',
            ],
            'Transmission' => [
                'Clutch Kit',
                'Flywheel',
                'Transmission Fluid',
                'Shift Cable',
                'Drive Shaft',
                'CV Joint',
                'Differential',
                'Axle Shaft',
                'Transfer Case',
                'Gear Oil',
            ],
            'Cooling' => [
                'Radiator',
                'Radiator Fan',
                'Coolant Reservoir',
                'Radiator Hose',
                'Cooling Fan Motor',
                'Temperature Sensor',
                'Water Neck',
                'Heater Core',
                'Radiator Cap',
                'Coolant',
            ],
        ];

        foreach ($categories as $category => $products) {
            foreach ($products as $index => $name) {
                Product::create([
                    'name' => $name,
                    'sku' => strtoupper(substr($category, 0, 3) . '-' . str_pad((string)($index + 1), 4, '0', STR_PAD_LEFT)),
                    'price' => $faker->randomFloat(2, 15, 800),
                    'stock_quantity' => $faker->numberBetween(5, 100),
                    'category' => $category,
                ]);
            }
        }
    }
}