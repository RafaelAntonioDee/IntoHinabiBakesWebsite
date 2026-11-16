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

        $this->view('pages/profile', [
            'pageTitle' => 'My Account - Sweet Cookie Oven',
            'user'      => $user,
        ]);
    }
}
