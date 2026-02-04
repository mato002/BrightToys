<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return;
        }

        // Array of diverse toy-related image URLs (using Unsplash Source for variety)
        $toyImages = [
            'https://images.unsplash.com/photo-1556912173-46e1c3c3c5e3?w=800&h=800&fit=crop',
            'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=800&h=800&fit=crop',
            'https://images.unsplash.com/photo-1530549387789-4c1017266635?w=800&h=800&fit=crop',
            'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=800&h=800&fit=crop',
            'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=800&h=800&fit=crop',
            'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?w=800&h=800&fit=crop',
            'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=800&h=800&fit=crop',
            'https://images.unsplash.com/photo-1556912173-46e1c3c3c5e3?w=800&h=800&fit=crop',
        ];

        $products = [
            // Baby & Toddler (0-3 yrs)
            [
                'name' => 'Rainbow Stack & Learn Blocks',
                'price' => 1490,
                'category' => 'Baby & Toddler (0-3 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Colorful stacking blocks that help develop fine motor skills and color recognition. Safe for babies with rounded edges and non-toxic materials.',
            ],
            [
                'name' => 'Soft Animal Rattle Set',
                'price' => 1290,
                'category' => 'Baby & Toddler (0-3 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Gentle rattles featuring cute animal designs. Perfect for sensory development and hand-eye coordination.',
            ],
            [
                'name' => 'Musical Activity Cube',
                'price' => 2490,
                'category' => 'Baby & Toddler (0-3 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Interactive cube with lights, sounds, and moving parts. Encourages exploration and cognitive development.',
            ],
            [
                'name' => 'First Words Picture Book Set',
                'price' => 1890,
                'category' => 'Baby & Toddler (0-3 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Durable board books with bright pictures and simple words. Perfect for early language development.',
            ],
            [
                'name' => 'Sensory Play Mat',
                'price' => 3290,
                'category' => 'Baby & Toddler (0-3 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Soft, colorful mat with different textures and patterns. Safe for tummy time and crawling practice.',
            ],

            // Pre-school (3-5 yrs)
            [
                'name' => 'Alphabet Learning Puzzle',
                'price' => 1890,
                'category' => 'Pre-school (3-5 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => '26-piece puzzle featuring uppercase and lowercase letters. Helps with letter recognition and matching skills.',
            ],
            [
                'name' => 'Colour Match Shape Sorter',
                'price' => 1750,
                'category' => 'Pre-school (3-5 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Classic shape sorting toy with vibrant colors. Develops problem-solving and color matching abilities.',
            ],
            [
                'name' => 'Play-Doh Fun Factory Set',
                'price' => 2190,
                'category' => 'Pre-school (3-5 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Creative play set with multiple colors and fun shapes. Encourages creativity and fine motor skills.',
            ],
            [
                'name' => 'Counting Bears Math Set',
                'price' => 1590,
                'category' => 'Pre-school (3-5 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Colorful bear counters with sorting cups. Perfect for early math skills and color recognition.',
            ],
            [
                'name' => 'Wooden Train Set',
                'price' => 3990,
                'category' => 'Pre-school (3-5 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Classic wooden train with tracks and accessories. Promotes imaginative play and spatial awareness.',
            ],

            // Primary (6-8 yrs)
            [
                'name' => 'LEGO Classic Creative Box',
                'price' => 4990,
                'category' => 'Primary (6-8 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => '500+ pieces in various colors. Endless building possibilities to spark creativity and engineering skills.',
            ],
            [
                'name' => 'Science Experiment Kit',
                'price' => 3490,
                'category' => 'Primary (6-8 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Hands-on experiments that make learning fun. Includes safe materials and step-by-step instructions.',
            ],
            [
                'name' => 'Art Supplies Deluxe Set',
                'price' => 2790,
                'category' => 'Primary (6-8 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Complete art set with crayons, markers, paints, and brushes. Everything needed for creative expression.',
            ],
            [
                'name' => 'Remote Control Car',
                'price' => 4290,
                'category' => 'Primary (6-8 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Fast and fun RC car with easy controls. Perfect for outdoor play and developing hand-eye coordination.',
            ],
            [
                'name' => 'Magic Tricks Set',
                'price' => 1990,
                'category' => 'Primary (6-8 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Complete magic kit with props and instructions. Builds confidence and presentation skills.',
            ],

            // Pre-teen (9-12 yrs)
            [
                'name' => 'Robotics Building Kit',
                'price' => 5990,
                'category' => 'Pre-teen (9-12 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Build and program your own robot. Introduces coding concepts and engineering principles.',
            ],
            [
                'name' => 'Strategy Board Game Collection',
                'price' => 3290,
                'category' => 'Pre-teen (9-12 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Collection of engaging board games that develop critical thinking and strategy skills.',
            ],
            [
                'name' => '3D Puzzle Architecture Set',
                'price' => 2790,
                'category' => 'Pre-teen (9-12 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Build famous landmarks with detailed 3D puzzles. Enhances spatial reasoning and patience.',
            ],
            [
                'name' => 'Craft & Jewelry Making Kit',
                'price' => 2490,
                'category' => 'Pre-teen (9-12 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Create beautiful jewelry with beads, charms, and tools. Encourages creativity and fine motor skills.',
            ],
            [
                'name' => 'Electronic Circuit Kit',
                'price' => 4490,
                'category' => 'Pre-teen (9-12 yrs)',
                'image' => '', // Will be assigned in loop
                'description' => 'Learn electronics by building circuits. Safe and educational introduction to electrical engineering.',
            ],

            // STEM & Educational Toys
            [
                'name' => 'Build & Glow Construction Set',
                'price' => 3290,
                'category' => 'STEM & Educational Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Glow-in-the-dark building blocks for creative construction. Combines engineering with visual appeal.',
            ],
            [
                'name' => 'Solar System Science Kit',
                'price' => 2990,
                'category' => 'STEM & Educational Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Explore the planets with this interactive model. Includes educational guide and fun facts.',
            ],
            [
                'name' => 'Microscope Discovery Set',
                'price' => 3990,
                'category' => 'STEM & Educational Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Real working microscope with prepared slides. Perfect for young scientists to explore the microscopic world.',
            ],
            [
                'name' => 'Coding Robot Mouse',
                'price' => 4790,
                'category' => 'STEM & Educational Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Programmable robot that teaches coding basics through play. Screen-free coding for beginners.',
            ],
            [
                'name' => 'Chemistry Lab Set',
                'price' => 5490,
                'category' => 'STEM & Educational Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Safe chemistry experiments with real lab equipment. Includes safety goggles and detailed instructions.',
            ],

            // Outdoor & Ride-ons
            [
                'name' => 'Outdoor Scooter – Blue Lightning',
                'price' => 4990,
                'category' => 'Outdoor & Ride-ons',
                'image' => '', // Will be assigned in loop
                'description' => 'Sturdy 3-wheel scooter with adjustable height. Perfect for developing balance and coordination.',
            ],
            [
                'name' => 'Foam Football Play Set',
                'price' => 1590,
                'category' => 'Outdoor & Ride-ons',
                'image' => '', // Will be assigned in loop
                'description' => 'Soft foam football with goal posts. Safe for indoor and outdoor play, promotes active fun.',
            ],
            [
                'name' => 'Balance Bike',
                'price' => 6990,
                'category' => 'Outdoor & Ride-ons',
                'image' => '', // Will be assigned in loop
                'description' => 'Pedal-free bike that teaches balance naturally. Adjustable seat grows with your child.',
            ],
            [
                'name' => 'Jump Rope & Hula Hoop Set',
                'price' => 1290,
                'category' => 'Outdoor & Ride-ons',
                'image' => '', // Will be assigned in loop
                'description' => 'Colorful set for active play. Great for developing coordination and cardiovascular fitness.',
            ],
            [
                'name' => 'Water Play Table',
                'price' => 4490,
                'category' => 'Outdoor & Ride-ons',
                'image' => '', // Will be assigned in loop
                'description' => 'Interactive water play station with accessories. Perfect for sensory play and cooling off.',
            ],

            // Puzzles & Board Games
            [
                'name' => 'Family Board Game Night Pack',
                'price' => 2590,
                'category' => 'Puzzles & Board Games',
                'image' => '', // Will be assigned in loop
                'description' => 'Collection of classic family games. Perfect for quality time and developing social skills.',
            ],
            [
                'name' => 'Mega Jigsaw Puzzle 100 Pieces',
                'price' => 1490,
                'category' => 'Puzzles & Board Games',
                'image' => '', // Will be assigned in loop
                'description' => 'Large format puzzle with vibrant artwork. Develops patience, focus, and problem-solving skills.',
            ],
            [
                'name' => 'Memory Matching Game',
                'price' => 1190,
                'category' => 'Puzzles & Board Games',
                'image' => '', // Will be assigned in loop
                'description' => 'Classic memory game with colorful cards. Enhances concentration and visual memory.',
            ],
            [
                'name' => 'Chess & Checkers Set',
                'price' => 1890,
                'category' => 'Puzzles & Board Games',
                'image' => '', // Will be assigned in loop
                'description' => 'Beautiful wooden set with both chess and checkers. Teaches strategy and critical thinking.',
            ],
            [
                'name' => 'Word Building Game',
                'price' => 2190,
                'category' => 'Puzzles & Board Games',
                'image' => '', // Will be assigned in loop
                'description' => 'Fun word game that builds vocabulary and spelling skills. Great for family game nights.',
            ],

            // Plush & Comfort Toys
            [
                'name' => 'Cuddly Bear Plush Friend',
                'price' => 1390,
                'category' => 'Plush & Comfort Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Super soft and huggable bear plush. Perfect companion for bedtime and comfort.',
            ],
            [
                'name' => 'Dreamland Unicorn Plush',
                'price' => 1890,
                'category' => 'Plush & Comfort Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Magical unicorn with rainbow mane. Soft, safe, and perfect for imaginative play.',
            ],
            [
                'name' => 'Dinosaur Plush Collection',
                'price' => 1690,
                'category' => 'Plush & Comfort Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Friendly dinosaur plush in multiple colors. Great for dinosaur enthusiasts and collectors.',
            ],
            [
                'name' => 'Sleepy Time Bunny',
                'price' => 1490,
                'category' => 'Plush & Comfort Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Gentle bunny perfect for bedtime. Soft and comforting for peaceful sleep.',
            ],
            [
                'name' => 'Interactive Talking Plush',
                'price' => 2790,
                'category' => 'Plush & Comfort Toys',
                'image' => '', // Will be assigned in loop
                'description' => 'Cuddly friend that responds to touch with sounds and phrases. Encourages interaction and play.',
            ],
        ];

        $imageIndex = 0;
        foreach ($products as $index => $data) {
            // Assign images sequentially, cycling through the array
            if (!isset($data['image']) || str_contains($data['image'], 'array_rand')) {
                $data['image'] = $toyImages[$imageIndex % count($toyImages)];
                $imageIndex++;
            } elseif (str_contains($data['image'], 'toyImages[')) {
                $data['image'] = $toyImages[$imageIndex % count($toyImages)];
                $imageIndex++;
            }
            $category = $categories->firstWhere('name', $data['category'])
                ?? $categories->random();

            Product::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'sku' => 'TOY-' . strtoupper(Str::random(6)),
                    'description' => $data['description'] ?? $data['name'] . ' - A fun and educational toy for children.',
                    'price' => $data['price'],
                    'stock' => rand(10, 100),
                    'category_id' => $category->id,
                    'image_url' => $data['image'] ?? null,
                    'featured' => rand(0, 1),
                    'status' => 'active',
                ]
            );
        }
    }
}
