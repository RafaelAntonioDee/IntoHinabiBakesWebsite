-- ==========================================================
-- Sweet Cookie Oven - Supabase PostgreSQL Database Setup
-- Run this script in your Supabase Dashboard:
-- 1. Go to your Supabase Project -> SQL Editor
-- 2. Click "New query"
-- 3. Paste this script and click "Run"
-- ==========================================================

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

-- 2. Contact Inquiries Table
CREATE TABLE IF NOT EXISTS contact_inquiries (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

-- 3. Products Table (Optional dynamic menu)
CREATE TABLE IF NOT EXISTS products (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'classic', -- 'classic', 'stuffed', 'box'
    description TEXT,
    price NUMERIC(10, 2) NOT NULL,
    unit VARCHAR(50) DEFAULT 'each',
    image_url TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

-- 4. Seed initial products if table is empty
INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Classic Chocolate Chunk', 'classic', 'Crispy edges, gooey center packed with dark Belgian chocolate chunks.', 3.50, 'each', 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=600&q=80'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Classic Chocolate Chunk');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Nutella Lava Cookie', 'stuffed', 'Rich hazelnut cocoa dough stuffed with a warm, melted Nutella center.', 4.50, 'each', 'https://images.unsplash.com/photo-1618923850107-d1a234d7a73a?auto=format&fit=crop&w=600&q=80'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Nutella Lava Cookie');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Double Chocolate Fudge', 'classic', 'Decadent cocoa dough loaded with white chocolate chips and fudge bites.', 3.75, 'each', 'https://images.unsplash.com/photo-1582623201359-74e936ed48c4?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGNob2NvbGF0ZSUyMGNvb2tpZXxlbnwwfHwwfHx8MA%3D%3D'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Double Chocolate Fudge');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Oatmeal Cinnamon Raisin', 'classic', 'Hearty rolled oats baked with sweet cinnamon, brown sugar, and plump raisins.', 3.25, 'each', 'https://www.eatingwell.com/thmb/rzJMZGIDf6XKf3-V24fqTXBu_u0=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/45684841-d040ba276fd24b599d44ebfb0b8a7696.jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Oatmeal Cinnamon Raisin');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Snickerdoodle Crisp', 'classic', 'Soft buttery sugar cookie rolled in aromatic cinnamon sugar and baked to perfection.', 3.25, 'each', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSJO6ZZfrgYicBFkKN49OQnKUwpcmO-IQXpeWdcIaFZXjTI82zy3Wz4uuP&s=10'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Snickerdoodle Crisp');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Red Velvet Cheesecake', 'stuffed', 'Soft red velvet cookie filled with a rich cream cheese center.', 4.75, 'each', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTohyTO2mWs-38YpkrLoaMUTs9D9fhbdO-BzgYrq0gKJL2Yc8tfracMyfg&s=10'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Red Velvet Cheesecake');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Salted Caramel Stuffed', 'stuffed', 'Peanut butter dough stuffed with gooey melted salted caramel and topped with sea salt.', 4.85, 'each', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSHR4JCHe1wdiz2dkRR9UL5eg5WJc0yyDKgUyiRE1XNdpo7r_HCb8Y6WOc&s=10'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Salted Caramel Stuffed');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Cookies & Cream Delight', 'stuffed', 'Crushed Oreo cookie dough surrounding a white chocolate truffle core.', 4.95, 'each', 'https://www.sixsistersstuff.com/wp-content/uploads/2022/09/Cookies-and-Cream-Cookies-1.jpg'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Cookies & Cream Delight');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Baker''s Dozen Variety Box', 'box', 'A custom gift box featuring 12 of our top-selling seasonal and classic cookies.', 38.00, 'box', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiyOsTmfitGQVDKmFqahJr4Z6YD13UIfR5HQgtcZ0MQlt5mU-1Qmyh6899&s=10'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Baker''s Dozen Variety Box');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Party Sampler Box', 'box', '24 mini cookies perfect for events, office gatherings, and birthday parties.', 45.00, 'box', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSkhfi9AbibQvPt7o1ClEjHEIL8DT-x9RhH3BXGBguFoAOa66q_WebjUCAh&s=10'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Party Sampler Box');

INSERT INTO products (name, category, description, price, unit, image_url)
SELECT 'Deluxe Stuffed Gift Tin', 'box', 'An assortment of 8 large stuffed cookies packaged in an elegant signature tin.', 34.00, 'tin', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQtDZuxHEMVWp5XTIA4jYwAnWNlvilGEWE04Zd431BNAIO-vFm_YbGkw4U&s=10'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Deluxe Stuffed Gift Tin');
