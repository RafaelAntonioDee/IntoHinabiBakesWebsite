<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class HomeController extends Controller {
    public function index(): void {
        $userName = Session::name() ?? 'Cookie Lover';
        $isLoggedIn = Session::check();
        
        $this->view('pages/home', [
            'pageTitle'  => 'Sweet Cookie Oven - Gourmet Cookies Baked Fresh Hourly',
            'isLoggedIn' => $isLoggedIn,
            'userName'   => $userName,
        ]);
    }
}
