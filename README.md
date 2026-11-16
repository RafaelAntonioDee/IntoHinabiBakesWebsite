# 🍪 Sweet Cookie Oven (IntoHinabiBakes)

A modern, MVC-structured PHP web application for Sweet Cookie Oven with separated frontend, backend controllers, models, and ready-to-use **Supabase (PostgreSQL)** integration.

---

## 📁 Architecture Overview

```text
IntoHinabiBakesWebsite/
├── .env.example              # Template environment variables
├── .env                      # Active credentials (DB credentials, Supabase keys)
├── config/
│   ├── app.php               # Bootstrap, helpers, PSR-4 autoloader, session
│   └── database.php          # Database configurations (Supabase + MySQL)
├── src/                      # ⚙️ BACKEND (OOP & MVC)
│   ├── Core/
│   │   ├── Database.php      # Unified PDO singleton (PostgreSQL & MySQL)
│   │   ├── Controller.php    # Base controller with view() and redirect()
│   │   ├── Model.php         # Base model with lazy DB connection
│   │   ├── Router.php        # Front-controller router with clean URLs
│   │   └── Session.php       # Authentication & flash message manager
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── AboutController.php
│   │   ├── ContactController.php
│   │   ├── AuthController.php
│   │   └── ProfileController.php
│   └── Models/
│       ├── User.php          # Handles user signup, lookup, auth
│       ├── ContactInquiry.php# Saves contact inquiries
│       └── Product.php       # Product catalog with dynamic DB fallback
├── views/                    # 🎨 FRONTEND TEMPLATES (Separated from business logic)
│   ├── layouts/
│   │   ├── header.php        # Navigation bar, mobile menu, flash alert notices
│   │   └── footer.php        # Footer, scripts
│   ├── pages/
│   │   ├── home.php          # Hero section, feature boxes
│   │   ├── products.php      # Menu items with filter tabs
│   │   ├── about.php         # Bakery story, rules, location
│   │   ├── contact.php       # Inquiry form with validation
│   │   └── profile.php       # User account dashboard
│   ├── auth/
│   │   ├── login.php         # Secure login view
│   │   └── register.php      # Registration form
│   └── errors/
│       └── 404.php           # Custom 404 Not Found page
├── public/                   # 🌐 WEB SERVER ENTRY (Document Root)
│   ├── assets/
│   │   ├── css/custom.css    # Stylesheets
│   │   └── js/main.js        # Dynamic UI scripts (mobile menu, filters)
│   ├── .htaccess             # Apache clean URL rewriting
│   ├── index.php             # Front controller (all web traffic enters here)
│   └── test_db.php           # Live database connection diagnostic page
└── database/
    ├── supabase_setup.sql    # PostgreSQL schema ready for Supabase SQL Editor
    └── mysql_setup.sql       # MySQL schema for local XAMPP backup
```

---

## ⚡ How to Connect to Supabase

Supabase provides cloud PostgreSQL. Your PHP environment has `pdo_pgsql` enabled, which allows direct, native connection through PDO!

### Step 1: Create your Tables in Supabase
1. Log in to your [Supabase Dashboard](https://supabase.com/dashboard).
2. Open your project.
3. Click **SQL Editor** on the left menu.
4. Click **New query**.
5. Copy the contents of [`database/supabase_setup.sql`](file:///c:/xampp/htdocs/IntoHinabiBakesWebsite/IntoHinabiBakesWebsite/database/supabase_setup.sql) and paste it into the editor.
6. Click **Run**. This will create the `users`, `contact_inquiries`, and `products` tables.

### Step 2: Get Your Supabase Database Credentials
1. In your Supabase Dashboard, click the **Settings (gear icon)** at the bottom left (or **Project Settings**).
2. Go to **Database**.
3. Under **Connection info**, copy:
   - **Host**: e.g., `db.your-project-ref.supabase.co`
   - **Port**: `5432` (or `6543` for connection pooler)
   - **Database name**: `postgres`
   - **User**: `postgres`
   - **Password**: The database password you chose when creating the project.

### Step 3: Configure `.env`
Open `.env` in the project root and update:

```env
# Set active database to supabase
DB_CONNECTION=supabase

SUPABASE_DB_HOST=db.your-project-ref.supabase.co
SUPABASE_DB_PORT=5432
SUPABASE_DB_NAME=postgres
SUPABASE_DB_USER=postgres
SUPABASE_DB_PASSWORD=your_actual_supabase_password
```

### Step 4: Verify the Connection
Open your browser and navigate to the built-in diagnostic page:
```
http://localhost/IntoHinabiBakesWebsite/IntoHinabiBakesWebsite/public/test_db.php
```
It will test the connection in real time and confirm whether Supabase is connected!

---

## 🚀 How to Run the Website

### Option A: Using XAMPP Apache
Make sure Apache is running in XAMPP Control Panel, then visit:
```
http://localhost/IntoHinabiBakesWebsite/
```
(It will automatically route to the `public/` directory).

### Option B: Using PHP Built-In Server (Recommended for quick dev)
Open a terminal in `IntoHinabiBakesWebsite/IntoHinabiBakesWebsite`:
```bash
php -S localhost:8000 -t public
```
Then visit `http://localhost:8000` in your browser!
