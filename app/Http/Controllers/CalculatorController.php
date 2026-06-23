<?php

namespace App\Http\Controllers;

use App\Models\HppProduct;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function index(): View
    {
        $products = HppProduct::active()->orderBy('name')->get();

        return view('calculator.index', compact('products'));
    }
}
