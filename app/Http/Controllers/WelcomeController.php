<?php

namespace App\Http\Controllers;

class WelcomeController extends Controller {
    
    public function index() {
        return auth()->check()
            ? redirect(route('home'))
            : view('login');
    }
}
