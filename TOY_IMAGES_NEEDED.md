# Toy Product Images Needed

Place all toy product images in: `public/images/toys/`

## Required Image Files (40 products):

### Baby & Toddler (0-3 yrs)
1. `rainbow-blocks.jpg` - Rainbow Stack & Learn Blocks
2. `animal-rattles.jpg` - Soft Animal Rattle Set
3. `activity-cube.jpg` - Musical Activity Cube
4. `picture-books.jpg` - First Words Picture Book Set
5. `play-mat.jpg` - Sensory Play Mat

### Pre-school (3-5 yrs)
6. `alphabet-puzzle.jpg` - Alphabet Learning Puzzle
7. `shape-sorter.jpg` - Colour Match Shape Sorter
8. `playdoh-set.jpg` - Play-Doh Fun Factory Set
9. `counting-bears.jpg` - Counting Bears Math Set
10. `train-set.jpg` - Wooden Train Set

### Primary (6-8 yrs)
11. `lego-classic.jpg` - LEGO Classic Creative Box
12. `science-kit.jpg` - Science Experiment Kit
13. `art-supplies.jpg` - Art Supplies Deluxe Set
14. `rc-car.jpg` - Remote Control Car
15. `magic-tricks.jpg` - Magic Tricks Set

### Pre-teen (9-12 yrs)
16. `robotics-kit.jpg` - Robotics Building Kit
17. `strategy-games.jpg` - Strategy Board Game Collection
18. `3d-puzzle.jpg` - 3D Puzzle Architecture Set
19. `jewelry-kit.jpg` - Craft & Jewelry Making Kit
20. `circuit-kit.jpg` - Electronic Circuit Kit

### STEM & Educational Toys
21. `construction-set.jpg` - Build & Glow Construction Set
22. `solar-system-kit.jpg` - Solar System Science Kit
23. `microscope.jpg` - Microscope Discovery Set
24. `coding-mouse.jpg` - Coding Robot Mouse
25. `chemistry-set.jpg` - Chemistry Lab Set

### Outdoor & Ride-ons
26. `kids-scooter.jpg` - Outdoor Scooter – Blue Lightning
27. `foam-football.jpg` - Foam Football Play Set
28. `balance-bike.jpg` - Balance Bike
29. `jump-rope.jpg` - Jump Rope & Hula Hoop Set
30. `water-table.jpg` - Water Play Table

### Puzzles & Board Games
31. `family-board-game.jpg` - Family Board Game Night Pack
32. `jigsaw-puzzle.jpg` - Mega Jigsaw Puzzle 100 Pieces
33. `memory-game.jpg` - Memory Matching Game
34. `chess-set.jpg` - Chess & Checkers Set
35. `word-game.jpg` - Word Building Game

### Plush & Comfort Toys
36. `bear-plush.jpg` - Cuddly Bear Plush Friend
37. `unicorn-plush.jpg` - Dreamland Unicorn Plush
38. `dinosaur-plush.jpg` - Dinosaur Plush Collection
39. `bunny-plush.jpg` - Sleepy Time Bunny
40. `talking-plush.jpg` - Interactive Talking Plush

## Image Requirements:
- Format: JPG/JPEG
- Recommended size: 800x800px or larger (square format works best)
- Quality: High resolution for clear product display
- Background: White or transparent preferred
- Content: Clear, well-lit product photos

## To Seed Products:
After adding images, run:
```bash
php artisan db:seed --class=ProductSeeder
```

Or to refresh everything:
```bash
php artisan migrate:fresh --seed
```
