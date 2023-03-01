<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class RumahController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('user.products', compact('products'));
        
    }
}
