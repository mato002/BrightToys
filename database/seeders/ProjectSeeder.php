<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the e-commerce project
        Project::firstOrCreate(
            ['slug' => 'brighttoys-ecommerce'],
            [
                'name' => 'BrightToys E-Commerce',
                'slug' => 'brighttoys-ecommerce',
                'description' => 'The main e-commerce platform for selling toys online. This is the primary revenue-generating project for the partnership.',
                'type' => 'ecommerce',
                'route_name' => 'home', // Laravel route name
                'icon' => 'fas fa-shopping-cart',
                'color' => 'emerald',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // You can add more projects here as needed
        // Example:
        // Project::firstOrCreate(
        //     ['slug' => 'another-project'],
        //     [
        //         'name' => 'Another Project',
        //         'slug' => 'another-project',
        //         'description' => 'Description here',
        //         'type' => 'service',
        //         'url' => 'https://example.com',
        //         'icon' => 'fas fa-globe',
        //         'color' => 'blue',
        //         'is_active' => true,
        //         'sort_order' => 2,
        //     ]
        // );
    }
}
