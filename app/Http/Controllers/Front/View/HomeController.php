<?php

namespace App\Http\Controllers\Front\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        return view('main.main');
    }
}
