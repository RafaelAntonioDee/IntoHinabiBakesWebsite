<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use Throwable;

class ProductReview extends Model {

    /**
     * Get all reviews for a product
     */
    public function getReviews(int $productId): array {
        try {
            $stmt = $this->getDb()->prepare("
                SELECT id, product_id, user_id, user_name, rating, comment, created_at
                FROM product_reviews
                WHERE product_id = :product_id
                ORDER BY created_at DESC
            ");
            $stmt->execute([':product_id' => $productId]);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            foreach ($reviews as &$r) {
                $r['date'] = $this->formatRelativeDate($r['created_at'] ?? '');
                $r['name'] = $r['user_name'];
                $r['rating'] = (int)$r['rating'];
            }
            return $reviews;
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get review summary (avg score, total count, star breakdowns)
     */
    public function getSummary(int $productId): array {
        try {
            $stmt = $this->getDb()->prepare("
                SELECT 
                    COUNT(*) as total_count,
                    COALESCE(ROUND(AVG(rating)::numeric, 1), 0.0) as average_rating,
                    COUNT(CASE WHEN rating = 5 THEN 1 END) as star_5,
                    COUNT(CASE WHEN rating = 4 THEN 1 END) as star_4,
                    COUNT(CASE WHEN rating = 3 THEN 1 END) as star_3,
                    COUNT(CASE WHEN rating = 2 THEN 1 END) as star_2,
                    COUNT(CASE WHEN rating = 1 THEN 1 END) as star_1
                FROM product_reviews
                WHERE product_id = :product_id
            ");
            $stmt->execute([':product_id' => $productId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $total = (int)($row['total_count'] ?? 0);
            $avg = (float)($row['average_rating'] ?? 0.0);
            $s5 = (int)($row['star_5'] ?? 0);
            $s4 = (int)($row['star_4'] ?? 0);
            $s3 = (int)($row['star_3'] ?? 0);
            $s2 = (int)($row['star_2'] ?? 0);
            $s1 = (int)($row['star_1'] ?? 0);

            return [
                'total_count'    => $total,
                'average_rating' => $total > 0 ? number_format($avg, 1) : '0.0',
                'stars' => [
                    5 => ['count' => $s5, 'pct' => $total > 0 ? (int)round(($s5 / $total) * 100) : 0],
                    4 => ['count' => $s4, 'pct' => $total > 0 ? (int)round(($s4 / $total) * 100) : 0],
                    3 => ['count' => $s3, 'pct' => $total > 0 ? (int)round(($s3 / $total) * 100) : 0],
                    2 => ['count' => $s2, 'pct' => $total > 0 ? (int)round(($s2 / $total) * 100) : 0],
                    1 => ['count' => $s1, 'pct' => $total > 0 ? (int)round(($s1 / $total) * 100) : 0],
                ]
            ];
        } catch (Throwable $e) {
            return [
                'total_count'    => 0,
                'average_rating' => '0.0',
                'stars' => [
                    5 => ['count' => 0, 'pct' => 0],
                    4 => ['count' => 0, 'pct' => 0],
                    3 => ['count' => 0, 'pct' => 0],
                    2 => ['count' => 0, 'pct' => 0],
                    1 => ['count' => 0, 'pct' => 0],
                ]
            ];
        }
    }

    /**
     * Get summaries and reviews for ALL products in grouped structure for fast frontend bootstrap
     */
    public function getAllReviewsData(?int $userId = null): array {
        try {
            $stmt = $this->getDb()->query("
                SELECT id, product_id, user_id, user_name, rating, comment, created_at
                FROM product_reviews
                ORDER BY created_at DESC
            ");
            $allReviews = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            $grouped = [];
            foreach ($allReviews as $r) {
                $pId = (int)$r['product_id'];
                if (!isset($grouped[$pId])) {
                    $grouped[$pId] = [
                        'reviews' => [],
                        'user_review' => null,
                        'counts' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
                        'total' => 0,
                        'rating_sum' => 0,
                    ];
                }
                $rating = max(1, min(5, (int)$r['rating']));
                $grouped[$pId]['reviews'][] = [
                    'id'      => (int)$r['id'],
                    'name'    => $r['user_name'],
                    'rating'  => $rating,
                    'comment' => $r['comment'],
                    'date'    => $this->formatRelativeDate($r['created_at']),
                    'user_id' => $r['user_id'] ? (int)$r['user_id'] : null,
                ];
                $grouped[$pId]['counts'][$rating]++;
                $grouped[$pId]['total']++;
                $grouped[$pId]['rating_sum'] += $rating;

                if ($userId !== null && (int)$r['user_id'] === $userId) {
                    $grouped[$pId]['user_review'] = [
                        'rating'  => $rating,
                        'comment' => $r['comment'],
                    ];
                }
            }

            $result = [];
            foreach ($grouped as $pId => $data) {
                $total = $data['total'];
                $avg = $total > 0 ? round($data['rating_sum'] / $total, 1) : 0.0;
                $result[$pId] = [
                    'total_count'    => $total,
                    'average_rating' => $total > 0 ? number_format($avg, 1) : '0.0',
                    'user_review'    => $data['user_review'],
                    'stars' => [
                        5 => ['count' => $data['counts'][5], 'pct' => $total > 0 ? (int)round(($data['counts'][5] / $total) * 100) : 0],
                        4 => ['count' => $data['counts'][4], 'pct' => $total > 0 ? (int)round(($data['counts'][4] / $total) * 100) : 0],
                        3 => ['count' => $data['counts'][3], 'pct' => $total > 0 ? (int)round(($data['counts'][3] / $total) * 100) : 0],
                        2 => ['count' => $data['counts'][2], 'pct' => $total > 0 ? (int)round(($data['counts'][2] / $total) * 100) : 0],
                        1 => ['count' => $data['counts'][1], 'pct' => $total > 0 ? (int)round(($data['counts'][1] / $total) * 100) : 0],
                    ],
                    'reviews' => $data['reviews'],
                ];
            }
            return $result;
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Save or update a review
     */
    public function saveReview(int $productId, ?int $userId, string $userName, int $rating, string $comment): bool {
        $db = $this->getDb();
        $rating = max(1, min(5, $rating));

        if ($userId !== null) {
            $chk = $db->prepare("SELECT id FROM product_reviews WHERE product_id = ? AND user_id = ? LIMIT 1");
            $chk->execute([$productId, $userId]);
            $existingId = $chk->fetchColumn();

            if ($existingId) {
                $stmt = $db->prepare("
                    UPDATE product_reviews 
                    SET rating = ?, comment = ?, user_name = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                return $stmt->execute([$rating, $comment, $userName, $existingId]);
            }
        }

        $stmt = $db->prepare("
            INSERT INTO product_reviews (product_id, user_id, user_name, rating, comment, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        return $stmt->execute([$productId, $userId, $userName, $rating, $comment]);
    }

    /**
     * Format timestamp to human relative date
     */
    private function formatRelativeDate(string $timestamp): string {
        if (!$timestamp) return 'Recently';
        $time = strtotime($timestamp);
        if (!$time) return 'Recently';
        $diff = time() - $time;

        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . ' mins ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
        return date('M j, Y', $time);
    }
}
