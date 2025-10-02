<?php

namespace App\Http\Controllers\Front\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clubhouse;

class CustomerController extends Controller
{
    public function registrasiNewCustomer(){
        $clubhouses = Clubhouse::all();
        return view('front.customer_data.registrasi_new_customer', compact('clubhouses'));
    }

    public function inputTelephone(){
        return view('front.customer_data.input_telephone');
    }
}
