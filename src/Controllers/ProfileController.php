<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Address;
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

        $addressModel = new Address();
        $addresses = $addressModel->getAllByUserId($userId);

        $this->view('pages/profile', [
            'pageTitle'    => 'My Account - Sweet Cookie Oven',
            'user'         => $user,
            'addresses'    => $addresses,
            'address'      => $addresses[0] ?? null,
            'maxAddresses' => Address::MAX_ADDRESSES_PER_USER,
        ]);
    }

    /**
     * Update user's full name from profile settings
     */
    public function updateName(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to update your profile.');
            $this->redirect('/login');
            return;
        }

        $userId = Session::id();
        $fullName = trim((string)($_POST['full_name'] ?? ''));

        if ($fullName === '') {
            flash('error', 'Please enter your full name.');
            $this->redirect('/profile');
            return;
        }

        $userModel = new User();
        if ($userModel->updateFullName($userId, $fullName)) {
            $_SESSION['user_name'] = $fullName;
            flash('success', 'Full name updated successfully.');
        } else {
            flash('error', 'Failed to update full name.');
        }

        $this->redirect('/profile');
    }

    /**
     * Update user's phone number from profile settings
     */
    public function updatePhone(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to update your profile.');
            $this->redirect('/login');
            return;
        }

        $userId = Session::id();
        $phoneNumber = trim((string)($_POST['phone_number'] ?? ''));

        if (!User::isValidPhoneNumber($phoneNumber)) {
            flash('error', 'Please enter a valid PH mobile (11 digits, e.g. 09171234567 or +639171234567) or landline number.');
            $this->redirect('/profile');
            return;
        }

        $userModel = new User();
        if ($userModel->updatePhoneNumber($userId, $phoneNumber)) {
            flash('success', 'Phone number updated successfully.');
        } else {
            flash('error', 'Failed to update phone number.');
        }

        $this->redirect('/profile');
    }

    /**
     * Update user's email address from profile settings
     */
    public function updateEmail(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to update your profile.');
            $this->redirect('/login');
            return;
        }

        $userId = Session::id();
        $email = trim((string)($_POST['email'] ?? ''));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            $this->redirect('/profile');
            return;
        }

        $userModel = new User();
        $existing = $userModel->findByEmail($email);
        if ($existing && (int)$existing['id'] !== $userId) {
            flash('error', 'That email address is already in use by another account.');
            $this->redirect('/profile');
            return;
        }

        if ($userModel->updateEmail($userId, $email)) {
            flash('success', 'Email address updated successfully.');
        } else {
            flash('error', 'Failed to update email address.');
        }

        $this->redirect('/profile');
    }

    /**
     * Create or update a customer delivery address (AJAX, authenticated users only)
     */
    public function saveAddress(): void {
        $userId = Session::id();
        if (!$userId) {
            $this->json([
                'success'       => false,
                'error'         => 'Please log in first to save a delivery address.',
                'require_login' => true,
            ], 401);
            return;
        }

        $input = $this->getJsonOrPostInput();

        $addressId = !empty($input['id']) ? (int)$input['id'] : null;
        $recipientName = trim((string)($input['recipient_name'] ?? ''));
        $street = trim((string)($input['street'] ?? $input['street_address'] ?? ''));
        $barangay = trim((string)($input['barangay'] ?? ''));
        $city = trim((string)($input['city'] ?? ''));
        $postalCode = trim((string)($input['postal_code'] ?? ''));
        $notes = trim((string)($input['notes'] ?? $input['delivery_notes'] ?? ''));
        $isDefault = !empty($input['is_default']);

        if ($street === '' && $barangay === '') {
            $this->json(['success' => false, 'error' => 'Please provide a valid street or barangay.'], 400);
            return;
        }

        if ($city === '') {
            $this->json(['success' => false, 'error' => 'Please specify your city.'], 400);
            return;
        }

        $payload = [
            'recipient_name' => $recipientName,
            'street'         => $street,
            'barangay'       => $barangay,
            'city'           => $city,
            'postal_code'    => $postalCode,
            'notes'          => $notes,
            'is_default'     => $isDefault,
        ];

        $addressModel = new Address();
        $result = $addressId
            ? $addressModel->updateForUser($addressId, $userId, $payload)
            : $addressModel->createForUser($userId, $payload);

        if (!$result['success']) {
            $status = str_contains($result['error'] ?? '', 'not found') ? 404 : 400;
            $this->json(['success' => false, 'error' => $result['error'] ?? 'Failed to save address.'], $status);
            return;
        }

        $savedAddress = $result['address'] ?? $payload;
        $_SESSION['confirmed_delivery_address'] = $savedAddress;

        $this->json([
            'success' => true,
            'message' => $addressId
                ? 'Delivery address updated successfully!'
                : 'Delivery address saved to your account successfully!',
            'address' => $savedAddress,
        ]);
    }

    /**
     * Set a saved address as default (AJAX, authenticated users only)
     */
    public function setDefaultAddress(): void {
        $userId = Session::id();
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Please log in first.', 'require_login' => true], 401);
            return;
        }

        $input = $this->getJsonOrPostInput();
        $addressId = (int)($input['id'] ?? 0);
        if ($addressId <= 0) {
            $this->json(['success' => false, 'error' => 'Invalid address ID.'], 400);
            return;
        }

        $addressModel = new Address();
        if (!$addressModel->setDefaultForUser($addressId, $userId)) {
            $this->json(['success' => false, 'error' => 'Address not found or could not be updated.'], 404);
            return;
        }

        $this->json(['success' => true, 'message' => 'Default delivery address updated.']);
    }

    /**
     * Delete a saved address (AJAX, authenticated users only)
     */
    public function deleteAddress(): void {
        $userId = Session::id();
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Please log in first.', 'require_login' => true], 401);
            return;
        }

        $input = $this->getJsonOrPostInput();
        $addressId = (int)($input['id'] ?? 0);
        if ($addressId <= 0) {
            $this->json(['success' => false, 'error' => 'Invalid address ID.'], 400);
            return;
        }

        $addressModel = new Address();
        if (!$addressModel->deleteForUser($addressId, $userId)) {
            $this->json(['success' => false, 'error' => 'Address not found or could not be deleted.'], 404);
            return;
        }

        $this->json(['success' => true, 'message' => 'Delivery address deleted.']);
    }

    private function getJsonOrPostInput(): array {
        $raw = file_get_contents('php://input');
        $decoded = $raw ? json_decode($raw, true) : null;
        return is_array($decoded) ? $decoded : $_POST;
    }
}
