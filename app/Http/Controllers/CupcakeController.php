<?php

namespace App\Http\Controllers;

use App\Models\Cupcake;
use Illuminate\Http\Request;

class CupcakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Cupcake::all());
    }


    

}
