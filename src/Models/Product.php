<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class Product extends Model {
    /**
     * Fallback array matching current bakery items
     */
    private array $defaultProducts = [
        [
            'id' => 1,
            'name' => 'Classic Chocolate Chunk',
            'category' => 'classic',
            'description' => 'Crispy edges, gooey center packed with dark Belgian chocolate chunks.',
            'price' => 3.50,
            'unit' => 'each',
            'image_url' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'id' => 2,
            'name' => 'Nutella Lava Cookie',
            'category' => 'stuffed',
            'description' => 'Rich hazelnut cocoa dough stuffed with a warm, melted Nutella center.',
            'price' => 4.50,
            'unit' => 'each',
            'image_url' => 'https://images.unsplash.com/photo-1618923850107-d1a234d7a73a?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'id' => 3,
            'name' => 'Double Chocolate Fudge',
            'category' => 'classic',
            'description' => 'Decadent cocoa dough loaded with white chocolate chips and fudge bites.',
            'price' => 3.75,
            'unit' => 'each',
            'image_url' => 'https://images.unsplash.com/photo-1582623201359-74e936ed48c4?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGNob2NvbGF0ZSUyMGNvb2tpZXxlbnwwfHwwfHx8MA%3D%3D',
        ],
        [
            'id' => 4,
            'name' => 'Oatmeal Cinnamon Raisin',
            'category' => 'classic',
            'description' => 'Hearty rolled oats baked with sweet cinnamon, brown sugar, and plump raisins.',
            'price' => 3.25,
            'unit' => 'each',
            'image_url' => 'https://www.eatingwell.com/thmb/rzJMZGIDf6XKf3-V24fqTXBu_u0=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/45684841-d040ba276fd24b599d44ebfb0b8a7696.jpg',
        ],
        [
            'id' => 5,
            'name' => 'Snickerdoodle Crisp',
            'category' => 'classic',
            'description' => 'Soft buttery sugar cookie rolled in aromatic cinnamon sugar and baked to perfection.',
            'price' => 3.25,
            'unit' => 'each',
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSJO6ZZfrgYicBFkKN49OQnKUwpcmO-IQXpeWdcIaFZXjTI82zy3Wz4uuP&s=10',
        ],
        [
            'id' => 6,
            'name' => 'Red Velvet Cheesecake',
            'category' => 'stuffed',
            'description' => 'Soft red velvet cookie filled with a rich cream cheese center.',
            'price' => 4.75,
            'unit' => 'each',
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTohyTO2mWs-38YpkrLoaMUTs9D9fhbdO-BzgYrq0gKJL2Yc8tfracMyfg&s=10',
        ],
        [
            'id' => 7,
            'name' => 'Salted Caramel Stuffed',
            'category' => 'stuffed',
            'description' => 'Peanut butter dough stuffed with gooey melted salted caramel and topped with sea salt.',
            'price' => 4.85,
            'unit' => 'each',
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSHR4JCHe1wdiz2dkRR9UL5eg5WJc0yyDKgUyiRE1XNdpo7r_HCb8Y6WOc&s=10',
        ],
        [
            'id' => 8,
            'name' => 'Cookies & Cream Delight',
            'category' => 'stuffed',
            'description' => 'Crushed Oreo cookie dough surrounding a white chocolate truffle core.',
            'price' => 4.95,
            'unit' => 'each',
            'image_url' => 'https://www.sixsistersstuff.com/wp-content/uploads/2022/09/Cookies-and-Cream-Cookies-1.jpg',
        ],
        [
            'id' => 9,
            'name' => 'Baker\'s Dozen Variety Box',
            'category' => 'box',
            'description' => 'A custom gift box featuring 12 of our top-selling seasonal and classic cookies.',
            'price' => 38.00,
            'unit' => 'box',
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiyOsTmfitGQVDKmFqahJr4Z6YD13UIfR5HQgtcZ0MQlt5mU-1Qmyh6899&s=10',
        ],
        [
            'id' => 10,
            'name' => 'Party Sampler Box',
            'category' => 'box',
            'description' => '24 mini cookies perfect for events, office gatherings, and birthday parties.',
            'price' => 45.00,
            'unit' => 'box',
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSkhfi9AbibQvPt7o1ClEjHEIL8DT-x9RhH3BXGBguFoAOa66q_WebjUCAh&s=10',
        ],
        [
            'id' => 11,
            'name' => 'Deluxe Stuffed Gift Tin',
            'category' => 'box',
            'description' => 'An assortment of 8 large stuffed cookies packaged in an elegant signature tin.',
            'price' => 34.00,
            'unit' => 'tin',
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQtDZuxHEMVWp5XTIA4jYwAnWNlvilGEWE04Zd431BNAIO-vFm_YbGkw4U&s=10',
        ],
    ];

    /**
     * Retrieve all products (from DB if table exists, otherwise default catalog)
     */
    public function all(): array {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->query("SELECT * FROM products WHERE is_active = TRUE ORDER BY id ASC");
                $rows = $stmt->fetchAll();
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (PDOException $e) {
                // Table doesn't exist yet or DB not populated, return default array safely
            }
        }

        return $this->defaultProducts;
    }

    /**
     * Retrieve products filtered by category
     */
    public function getByCategory(string $category): array {
        $all = $this->all();
        if ($category === 'all' || empty($category)) {
            return $all;
        }

        return array_values(array_filter($all, fn($p) => ($p['category'] ?? '') === $category));
    }
}
