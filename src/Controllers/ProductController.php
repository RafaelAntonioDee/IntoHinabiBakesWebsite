<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller {
    public function index(): void {
        $productModel = new Product();
        $products = $productModel->all();
        $categories = $productModel->getCategories();

        $this->view('pages/products', [
            'pageTitle'  => 'Cookies & Cakes Menu - Into Hinabi Bakes',
            'products'   => $products,
            'categories' => $categories,
        ]);
    }
}
