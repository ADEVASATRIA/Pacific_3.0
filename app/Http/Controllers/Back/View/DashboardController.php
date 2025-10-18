<?php

namespace App\Http\Controllers\Back\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // public function index(){
    //     return view('main.back_blank');
    // }

    public function index(){
        return view('main.back-office');
    }
}
