<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Cupcake;
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


        
        
        // cupcakes not in request are deleted
        // cupcakes in request
        
        
        // check if user in request is the owner of the cart
        
        $userId = $request->user()->id;
        
        $cart = Cart::where('user_id', $userId)->first();
        // dd($cart->user_id);
        // dd($userId);

        if($cart->user_id !== $userId) {
            dd("in if user not owner of cart");
            return response("Unauthorized", 403);
        } 

        
        
        // validation
        $validatedData = $request->validate([
            "cupcakes" => "required|array",
            "cupcakes.*.cupcake_id" => "required|exists:cupcakes,id",
            "cupcakes.*.quantity" => "required|integer|min:0"
        ]);
        
        // dd("after");

        dd($validatedData);

        // get cupcakes in cart
        $currentCupcakes = $cart->cupcakes()->get();
        dd($currentCupcakes);





        $outOfStockCupcakes = [];

        foreach($validatedData["cupcakes"] as $item) {
            $cupcake = Cupcake::find($item["cupcake_id"]);


            if($cupcake->quantity < $item["quantity"]) {
                $outOfStockCupcakes[] = [
                    "id" => $cupcake->id,
                    "name" => $cupcake->name,
                    "requested_quantity" => $item["quantity"],
                    "available_quantity" => $cupcake->quantity
                ];

                continue;
            }

            $cart->cupcakes()->syncWithoutDetaching([
                $cupcake->id => ["quantity" => $item["quantity"]]
            ]);

        }

        if(!empty($outOfStockCupcakes)) {
            return response()->json([
                "message" => "Certains cupcakes ne sont pas disponibles en quantité suffisante",
                "outOfStockCupcales" => $outOfStockCupcakes
            ], 400);
        }


        $cart = Cart::where('user_id', $userId)->first();



        return $cart->update($validatedData);








     


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
