<?php
namespace App\Http\Controllers\Back\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('back.report.index');
    }
}