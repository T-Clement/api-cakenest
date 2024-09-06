<?php

namespace App\Http\Controllers;

use App\Models\Cupcake;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $user = $request->user();
        if($user->is_admin) {
            return Order::with(['cupcakes', 'user'])->get();
        } else {
            // return Order::where);
            Order::with(['cupcakes', 'user'])->where('user_id', "=", $user->id)->get();    
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate data
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cupcakes' => 'required|array',
            'cupcakes.*.cupcake_id' => 'required|exists:cupcakes,id',
            'cupcakes.*.quantity' => 'required|integer|min:1'
        ]);

        // array to return to user if stock for a specific cupcake is not available
        $outOfStockCupcakes = [];

        // check quantity in database is available for this order
        foreach($validated["cupcakes"] as $orderCupcake) {  
            // get cupcake in database 
            $cupcake = Cupcake::find($orderCupcake['cupcake_id']);
            // check quantity
            if($cupcake && $cupcake->quantity < $orderCupcake['quantity']) {
                $outOfStockCupcakes[] = [
                    'id' => $cupcake->id,
                    'name' => $cupcake->name,
                    'requested_quantity' => $orderCupcake['quantity'],
                    'available_stock' => $cupcake->quantity
                ];       
            }

        }


        // if cupcakes with not enough stock, return info to user
        if(!empty($outOfStockCupcakes)) {
            return response()->json([
                "message" => "Certains cupcakes ne sont pas disponibles en quantité suffisante",
                "outOfStockCupcakes" => $outOfStockCupcakes
            ], 400);
            
        }



        $user = $request->user();

        // case order from admin page, where admin select an id and create odrer for this specific user
        // if user_id field is in request, the order is comming from the admin dashboard
        if($user->is_admin && $request->has('user_id')) {

            $userId = $request->input('user_id');
            
        } else {
            // case normal, order created with data comming from store page
            $userId = $user->id;
        }


        // create new Order ans associate new user
        $order = Order::create([
            'user_id' => $userId
        ]);


        // foreach cupcake in order, insert a new row in pivot table cupcake_order
        foreach($validated['cupcakes'] as $orderCupcake) {
            // get cupcake from database
            $cupcake = Cupcake::findOrFail($orderCupcake['cupcake_id']);

            // 
            $order->cupcakes()->attach($orderCupcake['cupcake_id'], [
                'quantity' => $orderCupcake['quantity'],
                'total_price_in_cents' => $cupcake->price_in_cents * $orderCupcake['quantity'], // calculate total_price with price from database and
                'current_cupcake_price_when_order' => $cupcake->price_in_cents 
            ]);
            
            // lower stock of specific cupcake related to quantity in order for this cupcake
            $cupcake->quantity -= $orderCupcake['quantity'];
            $cupcake->save(); 
            
        }
        
        // return order with cupcakes
        return response()->json($order->load('cupcakes'), 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
