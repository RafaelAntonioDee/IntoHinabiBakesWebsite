<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class ProfileController extends Controller {
    public function index(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to access your profile.');
            $this->redirect('/login');
            return;
        }

        $userId = Session::id();
        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            Session::logout();
            flash('error', 'User profile not found. Please log in again.');
            $this->redirect('/login');
            return;
        }

        $addressModel = new \App\Models\Address();
        $address = $addressModel->getByUserId($userId);

        $this->view('pages/profile', [
            'pageTitle' => 'My Account - Sweet Cookie Oven',
            'user'      => $user,
            'address'   => $address,
        ]);
    }

    /**
     * Save customer delivery address (AJAX)
     */
    public function saveAddress(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $street = trim($input['street'] ?? $input['street_address'] ?? '');
        $city = trim($input['city'] ?? '');
        $barangay = trim($input['barangay'] ?? '');
        $notes = trim($input['notes'] ?? $input['delivery_notes'] ?? '');
        $lat = isset($input['lat']) ? $input['lat'] : null;
        $lng = isset($input['lng']) ? $input['lng'] : null;

        if (empty($street) && empty($barangay)) {
            $this->json(['success' => false, 'error' => 'Please provide a valid street or barangay.'], 400);
            return;
        }

        if (empty($city)) {
            $this->json(['success' => false, 'error' => 'Please specify your city.'], 400);
            return;
        }

        $userId = Session::id();
        if ($userId) {
            $addressModel = new \App\Models\Address();
            $saved = $addressModel->saveForUser($userId, [
                'street'   => $street,
                'barangay' => $barangay,
                'city'     => $city,
                'notes'    => $notes,
                'lat'      => $lat,
                'lng'      => $lng,
            ]);

            if ($saved) {
                $_SESSION['confirmed_delivery_address'] = [
                    'street'   => $street,
                    'barangay' => $barangay,
                    'city'     => $city,
                    'notes'    => $notes,
                    'lat'      => $lat,
                    'lng'      => $lng,
                ];

                $this->json([
                    'success' => true,
                    'message' => 'Delivery address saved to your account details successfully!',
                    'address' => $_SESSION['confirmed_delivery_address'],
                ]);
                return;
            } else {
                $this->json(['success' => false, 'error' => 'Failed to save address to database.'], 500);
                return;
            }
        }

        // Guest user: save in session
        $_SESSION['confirmed_delivery_address'] = [
            'street'   => $street,
            'barangay' => $barangay,
            'city'     => $city,
            'notes'    => $notes,
            'lat'      => $lat,
            'lng'      => $lng,
        ];

        $this->json([
            'success' => true,
            'message' => 'Delivery address saved for this session.',
            'address' => $_SESSION['confirmed_delivery_address'],
        ]);
    }
}
