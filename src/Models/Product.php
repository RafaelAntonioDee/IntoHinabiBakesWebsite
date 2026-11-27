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
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'Crispy edges, gooey center packed with dark Belgian chocolate chunks.',
            'price' => 3.50,
            'unit' => 'each',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'id' => 2,
            'name' => 'Nutella Lava Cookie',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'Rich hazelnut cocoa dough stuffed with a warm, melted Nutella center.',
            'price' => 4.50,
            'unit' => 'each',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://images.unsplash.com/photo-1618923850107-d1a234d7a73a?auto=format&fit=crop&w=600&q=80',
        ],
        [
            'id' => 3,
            'name' => 'Double Chocolate Fudge',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'Decadent cocoa dough loaded with white chocolate chips and fudge bites.',
            'price' => 3.75,
            'unit' => 'each',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://images.unsplash.com/photo-1582623201359-74e936ed48c4?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGNob2NvbGF0ZSUyMGNvb2tpZXxlbnwwfHwwfHx8MA%3D%3D',
        ],
        [
            'id' => 4,
            'name' => 'Oatmeal Cinnamon Raisin',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'Hearty rolled oats baked with sweet cinnamon, brown sugar, and plump raisins.',
            'price' => 3.25,
            'unit' => 'each',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://www.eatingwell.com/thmb/rzJMZGIDf6XKf3-V24fqTXBu_u0=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/45684841-d040ba276fd24b599d44ebfb0b8a7696.jpg',
        ],
        [
            'id' => 5,
            'name' => 'Snickerdoodle Crisp',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'Soft buttery sugar cookie rolled in aromatic cinnamon sugar and baked to perfection.',
            'price' => 3.25,
            'unit' => 'each',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSJO6ZZfrgYicBFkKN49OQnKUwpcmO-IQXpeWdcIaFZXjTI82zy3Wz4uuP&s=10',
        ],
        [
            'id' => 6,
            'name' => 'Red Velvet Cheesecake',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'Soft red velvet cookie filled with a rich cream cheese center.',
            'price' => 4.75,
            'unit' => 'each',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTohyTO2mWs-38YpkrLoaMUTs9D9fhbdO-BzgYrq0gKJL2Yc8tfracMyfg&s=10',
        ],
        [
            'id' => 7,
            'name' => 'Salted Caramel Stuffed',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'Peanut butter dough stuffed with gooey melted salted caramel and topped with sea salt.',
            'price' => 4.85,
            'unit' => 'each',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSHR4JCHe1wdiz2dkRR9UL5eg5WJc0yyDKgUyiRE1XNdpo7r_HCb8Y6WOc&s=10',
        ],
        [
            'id' => 8,
            'name' => 'Cookies & Cream Delight',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'Crushed Oreo cookie dough surrounding a white chocolate truffle core.',
            'price' => 4.95,
            'unit' => 'each',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://www.sixsistersstuff.com/wp-content/uploads/2022/09/Cookies-and-Cream-Cookies-1.jpg',
        ],
        [
            'id' => 9,
            'name' => 'Baker\'s Dozen Variety Box',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'A custom gift box featuring 12 of our top-selling seasonal and classic cookies.',
            'price' => 38.00,
            'unit' => 'box',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiyOsTmfitGQVDKmFqahJr4Z6YD13UIfR5HQgtcZ0MQlt5mU-1Qmyh6899&s=10',
        ],
        [
            'id' => 10,
            'name' => 'Party Sampler Box',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => '24 mini cookies perfect for events, office gatherings, and birthday parties.',
            'price' => 45.00,
            'unit' => 'box',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSkhfi9AbibQvPt7o1ClEjHEIL8DT-x9RhH3BXGBguFoAOa66q_WebjUCAh&s=10',
        ],
        [
            'id' => 11,
            'name' => 'Deluxe Stuffed Gift Tin',
            'category_id' => 1,
            'category' => 'cookies',
            'category_name' => 'Cookies',
            'description' => 'An assortment of 8 large stuffed cookies packaged in an elegant signature tin.',
            'price' => 34.00,
            'unit' => 'tin',
            'is_active' => true,
            'availability' => true,
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQtDZuxHEMVWp5XTIA4jYwAnWNlvilGEWE04Zd431BNAIO-vFm_YbGkw4U&s=10',
        ],
    ];

    /**
     * Retrieve all active products for the public storefront
     */
    public function all(): array {
        if ($this->db !== null) {
            try {
                $sql = "SELECT p.*, 
                               COALESCE(LOWER(TRIM(pc.category_name)), 'cookies') AS category,
                               COALESCE(TRIM(pc.category_name), 'Cookies') AS category_name
                        FROM products p
                        LEFT JOIN product_categories pc ON p.category_id = pc.id
                        WHERE p.is_active = TRUE
                        ORDER BY p.category_id ASC, p.id ASC";
                $stmt = $this->db->query($sql);
                $rows = $stmt->fetchAll();
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (PDOException $e) {
                // Fallback to default
            }
        }

        return $this->defaultProducts;
    }

    /**
     * Retrieve all products for admin/baker inventory management (including inactive/availability)
     */
    public function allForManagement(?int $categoryId = null, ?string $search = null): array {
        if ($this->db !== null) {
            try {
                $sql = "SELECT p.*, 
                               COALESCE(LOWER(TRIM(pc.category_name)), 'cookies') AS category,
                               COALESCE(TRIM(pc.category_name), 'Cookies') AS category_name
                        FROM products p
                        LEFT JOIN product_categories pc ON p.category_id = pc.id
                        WHERE 1=1";
                $params = [];

                if ($categoryId !== null && $categoryId > 0) {
                    $sql .= " AND p.category_id = :category_id";
                    $params[':category_id'] = $categoryId;
                }

                if (!empty($search)) {
                    $sql .= " AND (LOWER(p.name) LIKE :search OR LOWER(p.description) LIKE :search)";
                    $params[':search'] = '%' . strtolower(trim($search)) . '%';
                }

                $sql .= " ORDER BY p.category_id ASC, p.id ASC";

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll() ?: [];
            } catch (PDOException $e) {
                // Fallback
            }
        }

        return $this->defaultProducts;
    }

    /**
     * Find single product by ID
     */
    public function findById(int $id): ?array {
        if ($this->db !== null) {
            try {
                $sql = "SELECT p.*, 
                               COALESCE(LOWER(TRIM(pc.category_name)), 'cookies') AS category,
                               COALESCE(TRIM(pc.category_name), 'Cookies') AS category_name
                        FROM products p
                        LEFT JOIN product_categories pc ON p.category_id = pc.id
                        WHERE p.id = :id 
                        LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':id' => $id]);
                $product = $stmt->fetch();
                return $product ?: null;
            } catch (PDOException $e) {
                return null;
            }
        }

        foreach ($this->defaultProducts as $p) {
            if ($p['id'] == $id) return $p;
        }
        return null;
    }

    /**
     * Toggle product availability (In Stock / Sold Out)
     */
    public function toggleAvailability(int $id): bool {
        if ($this->db !== null) {
            try {
                $product = $this->findById($id);
                if (!$product) return false;

                // Toggle availability (default to is_active if column doesn't exist)
                $current = !empty($product['availability']);
                $newVal = !$current;

                $stmt = $this->db->prepare("UPDATE products SET availability = :avail WHERE id = :id");
                return $stmt->execute([
                    ':avail' => $newVal ? 1 : 0,
                    ':id'    => $id,
                ]);
            } catch (PDOException $e) {
                // Fallback toggle on is_active
                try {
                    $stmt = $this->db->prepare("UPDATE products SET is_active = NOT is_active WHERE id = :id");
                    return $stmt->execute([':id' => $id]);
                } catch (\Throwable $ex) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Create product
     */
    public function createProduct(array $data): bool {
        if ($this->db === null) return false;
        try {
            $sql = "INSERT INTO products (name, description, allergens, category_id, price, image_url, is_active, availability, created_at) 
                    VALUES (:name, :description, :allergens, :category_id, :price, :image_url, :is_active, :availability, CURRENT_TIMESTAMP)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name'         => $data['name'],
                ':description'  => $data['description'] ?? '',
                ':allergens'    => $data['allergens'] ?? '',
                ':category_id'  => $data['category_id'] ?? 1,
                ':price'        => $data['price'] ?? 0.00,
                ':image_url'    => $data['image_url'] ?? '',
                ':is_active'    => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                ':availability' => isset($data['availability']) ? (int)$data['availability'] : 1,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update product
     */
    public function updateProduct(int $id, array $data): bool {
        if ($this->db === null) return false;
        try {
            $sql = "UPDATE products 
                    SET name = :name, 
                        description = :description, 
                        allergens = :allergens, 
                        category_id = :category_id, 
                        price = :price, 
                        image_url = :image_url, 
                        is_active = :is_active, 
                        availability = :availability 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':name'         => $data['name'],
                ':description'  => $data['description'] ?? '',
                ':allergens'    => $data['allergens'] ?? '',
                ':category_id'  => $data['category_id'] ?? 1,
                ':price'        => $data['price'] ?? 0.00,
                ':image_url'    => $data['image_url'] ?? '',
                ':is_active'    => isset($data['is_active']) ? (int)$data['is_active'] : 1,
                ':availability' => isset($data['availability']) ? (int)$data['availability'] : 1,
                ':id'           => $id,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct(int $id): bool {
        if ($this->db === null) return false;
        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Retrieve all categories directly from product_categories table
     */
    public function getCategories(): array {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->query("SELECT id, TRIM(category_name) AS category_name FROM product_categories ORDER BY id ASC");
                $rows = $stmt->fetchAll();
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (PDOException $e) {
                // Fallback
            }
        }

        return [
            ['id' => 1, 'category_name' => 'Cookies'],
            ['id' => 2, 'category_name' => 'Cakes'],
        ];
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
