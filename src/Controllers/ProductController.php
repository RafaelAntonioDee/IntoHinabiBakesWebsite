<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Product;
use App\Models\ProductReview;

class ProductController extends Controller {
    public function index(): void {
        $productModel = new Product();
        $products = $productModel->all();
        $categories = $productModel->getCategories();

        $userId = Session::id();
        $reviewModel = new ProductReview();
        $reviewsData = $reviewModel->getAllReviewsData($userId);

        $this->view('pages/products', [
            'pageTitle'    => 'Cookies & Cakes Menu - Into Hinabi Bakes',
            'products'     => $products,
            'categories'   => $categories,
            'reviewsData'  => $reviewsData,
        ]);
    }

    public function submitReview(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $productId = (int)($input['product_id'] ?? 0);
        $rating = (int)($input['rating'] ?? 5);
        $comment = trim($input['comment'] ?? '');

        if ($productId <= 0 || empty($comment)) {
            $this->json(['success' => false, 'error' => 'Please provide a valid comment and rating.'], 400);
            return;
        }

        if (!Session::check() || !Session::id()) {
            $this->json([
                'success' => false, 
                'error' => 'You must be logged in to leave a review.',
                'requires_login' => true
            ], 401);
            return;
        }

        $userId = Session::id();

        $reviewModel = new ProductReview();
        $saved = $reviewModel->saveReview($productId, $userId, $rating, $comment);

        if (!$saved) {
            $this->json(['success' => false, 'error' => 'Failed to save review.'], 500);
            return;
        }

        // Return refreshed reviews list and summary for this product
        $summary = $reviewModel->getSummary($productId);
        $reviews = $reviewModel->getReviews($productId);

        $this->json([
            'success' => true,
            'message' => 'Review successfully saved!',
            'data' => [
                'total_count'    => $summary['total_count'],
                'average_rating' => $summary['average_rating'],
                'stars'          => $summary['stars'],
                'reviews'        => $reviews,
                'user_review'    => [
                    'rating'  => $rating,
                    'comment' => $comment,
                ]
            ]
        ]);
    }
}
