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

        // check if user in request is the owner of the cart

        $userId = $request->user()->id;

        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart || $cart->user_id !== $userId) {
            dd("in if user not owner of cart");
            return response("Unauthorized", 403);
        }



        // validation
        $validatedData = $request->validate([
            "cupcakes" => "required|array",
            "cupcakes.*.cupcake_id" => "required|exists:cupcakes,id",
            "cupcakes.*.quantity" => "required|integer|min:0"
        ]);


        // dd($validatedData);

        // get cupcakes in cart
        // $currentCupcakes = $cart->cupcakes()->get();
        $currentCupcakes = $cart->cupcakes()->get()->keyBy('id');
        // dd($currentCupcakes->toArray()); // cart can be empty



        // cupcakes coming from request
        $newCupcakes = collect($validatedData['cupcakes'])->keyBy('cupcake_id');


        // dd($newCupcakes);



        $outOfStockCupcakes = [];



        // foreach($currentCupcakes as $index => $cupcakeInCart) {
        foreach ($currentCupcakes as $cupcakeId => $cupcakeInCart) {
            // $oldQuantity = $cupcakeInCart->pivot->quantity;
            $newItem = $newCupcakes->get($cupcakeId);
            // dd($newItem);



            if (!$newItem) {
                // cupcake is no more asked
                    // remove it from cart
                // $cart->cupcakes()->detach($cupcakeId);
            } else {
                $newQuantity = $newItem["quantity"];

                if ($newQuantity === 0) {
                    $cart->cupcakes()->detach($cupcakeId);
                } else {
                    // stock is not handled in cart so it remove some complexity
                    // it is handle in order.store

                    // si la quantité du cupcake en stock en base est inférieure à la quantité en formulaire
                    // retourner une info du style outOfStock
                    // faire la requete sur le cupcake en base
                    $cupcake = Cupcake::find($cupcakeId);

                    if ($newQuantity > $cupcake->quantity) {

                        // NOT ENOUGH CUPCAKES IN STOCK
                        // return the data about the stock of this cupcake not beeing enough 
                        // ...

                        $outOfStockCupcakes[] = [
                            'id' => $cupcake->id,
                            'name' => $cupcake->name,
                            'requested_quantity' => $newQuantity,
                            'available_stock' => $cupcake->quantity,
                        ];
                    } else {

                        // update the quantity of this cupcake in cart
                        // ...
                        $cart->cupcakes()->updateExistingPivot($cupcakeId, ["quantity" => $newQuantity]);
                    }
                }

                // cupcake handle so we remove it from collection
                $newCupcakes->forget($cupcakeId);
            }
        }


        // ADD NEW CUPCAKES
        // take the resting cupcakes in collection because they are not already in kart
        foreach ($newCupcakes as $cupcakeId => $item) {
            $newQuantity = $item['quantity'];


            // MAKE A TEST FOR THIS CASE
            // MAKE A TEST FOR THIS CASE
            // MAKE A TEST FOR THIS CASE
            // MAKE A TEST FOR THIS CASE
            if ($newQuantity === 0) {
                // we dont add the cupcake if quantity equals 0
                continue;
            }


            //check stock
            $cupcake = Cupcake::find($cupcakeId);

            if ($newQuantity > $cupcake->quantity) {
                // NOT ENOUGH IN STOCK
                $outOfStockCupcakes[] = [
                    'id' => $cupcake->id,
                    'name' => $cupcake->name,
                    'requested_quantity' => $newQuantity,
                    'available_stock' => $cupcake->quantity,
                ];
            } else {
                // cupcakes quantity asked is in stock
                $cart->cupcakes()->attach($cupcakeId, ["quantity" => $newQuantity]);
            }
        }

        // IF STOCK ISSUE
        if (!empty($outOfStockCupcakes)) {
            return response()->json([
                "message" => "Certains cupcakes ne sont pas disponibles en quantité suffisante",
                "outOfStockCupcakes" => $outOfStockCupcakes
            ], 400);
        }

        // RESPONSE
        return response()->json($cart->load('cupcakes'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
