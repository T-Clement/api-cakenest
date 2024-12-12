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
    // public function index()
    // {
    //     //
    // }



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

        if($request->id != $userId) {
            return response("Forbidden", 403);
        }

        $cart = Cart::where('user_id', $userId)->first();

        if(!$cart) {
            return response("No cart found", 404);
        }

        return response()->json($cart->load("cupcakes"), 200);
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        
        // id of user making the request
        $userId = $request->user()->id;
        
        // take id of user in params in request and not the id of the user making the request
        $cart = Cart::where('user_id', $request->id)->first();
        
        // check if user in request is the owner of the cart
        if ((!$cart || $cart->user_id !== $userId) && !$request->user()->is_admin) {
            // dd("in if user not owner of cart");
            return response("Unauthorized", 403);
        }


        // validation
        $validatedData = $request->validate([
            "cupcakes" => "required|array",
            "cupcakes.*.cupcake_id" => "required|exists:cupcakes,id",
            "cupcakes.*.quantity" => "required|integer|min:0"
        ]);


        // get cupcakes in cart
        $currentCupcakes = $cart->cupcakes()->get()->keyBy('id'); // cart can be empty

        // cupcakes coming from request
            // create a collection to manipulate easily the data with specials methods
        $newCupcakes = collect($validatedData['cupcakes'])->keyBy('cupcake_id');


        $outOfStockCupcakes = [];

        foreach ($currentCupcakes as $cupcakeId => $cupcakeInCart) {
            $newItem = $newCupcakes->get($cupcakeId);


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

                        $outOfStockCupcakes[] = [
                            'id' => $cupcake->id,
                            'name' => $cupcake->name,
                            'requested_quantity' => $newQuantity,
                            'available_stock' => $cupcake->quantity,
                        ];

                    } else {

                        // update the quantity of this cupcake in cart
                        $cart->cupcakes()->updateExistingPivot($cupcakeId, ["quantity" => $newQuantity]);
                    }
                }

                // cupcake handle so we remove it from collection, cupcakes remaining are for an other foreach
                $newCupcakes->forget($cupcakeId);
            }
        }


        // ADD NEW CUPCAKES
        // !!!!!!!!
        // take the resting cupcakes in collection because they are not already in kart
        // !!!!!!!!
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
    // public function destroy(Cart $cart)
    // {
    //     //
    // }
}
