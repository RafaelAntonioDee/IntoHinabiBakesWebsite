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

        // Validation (check all fields at once)
        $errors = [];
        if ($name === '') {
            $errors['name'] = 'Full name is required.';
        }
        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if ($message === '') {
            $errors['message'] = 'Message is required.';
        }

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors, 'error' => reset($errors)], 400);
            }
            flash('error', implode(' ', $errors));
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email, 'message' => $message];
            $this->redirect('/contact');
            return;
        }

        try {
            $inquiry = new ContactInquiry();
            $inquiry->create($name, $email, $message);
            unset($_SESSION['old_input']);
            $msg = "Inquiry submitted successfully! We'll get back to you soon.";
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => $msg]);
            }
            flash('success', $msg);
        } catch (\Throwable $e) {
            $err = 'Error saving your message to the database: ' . $e->getMessage();
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 500);
            }
            flash('error', $err);
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email, 'message' => $message];
        }

        $this->redirect('/contact');
    }
}
