<?php

namespace App\Controllers;

use App\Core\Controller;

class AboutController extends Controller {
    public function index(): void {
        $this->view('pages/about', [
            'pageTitle' => 'Behind the Oven - Sweet Cookie Oven',
        ]);
    }
}
