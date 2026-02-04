<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Baby & Toddler (0-3 yrs)',
            'Pre-school (3-5 yrs)',
            'Primary (6-8 yrs)',
            'Pre-teen (9-12 yrs)',
            'STEM & Educational Toys',
            'Outdoor & Ride-ons',
            'Puzzles & Board Games',
            'Plush & Comfort Toys',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $name.' category',
                ]
            );
        }
    }
}

