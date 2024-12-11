<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use COM;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        // take user in request

        $user = $request->user();
        // dd($user->toArray());

        $cart = new Cart;

        $cart->user_id = $user->id;
        $cart->total = 0;

        // dd($cart);
        $cart->save();


        return response()->json($cart, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $userId = $request->user()->id;
        // dd($user->toArray());

        
        $cart = Cart::where('user_id', $userId)->first();
        
        // dd($cart->load("cupcakes")->toArray());

        return $cart->load("cupcakes");

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
