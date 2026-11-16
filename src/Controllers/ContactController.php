<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ContactInquiry;

class ContactController extends Controller {
    public function index(): void {
        $this->view('pages/contact', [
            'pageTitle' => 'Get In Touch - Sweet Cookie Oven',
        ]);
    }

    public function submit(): void {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validation
        if (empty($name) || empty($email) || empty($message)) {
            flash('error', 'Please fill in all required fields.');
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email, 'message' => $message];
            $this->redirect('/contact');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email, 'message' => $message];
            $this->redirect('/contact');
            return;
        }

        try {
            $inquiry = new ContactInquiry();
            $inquiry->create($name, $email, $message);
            unset($_SESSION['old_input']);
            flash('success', "Inquiry submitted successfully! We'll get back to you soon.");
        } catch (\Throwable $e) {
            flash('error', 'Error saving your message to the database: ' . $e->getMessage());
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email, 'message' => $message];
        }

        $this->redirect('/contact');
    }
}
