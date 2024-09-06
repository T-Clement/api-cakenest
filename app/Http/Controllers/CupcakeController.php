<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cupcake\StoreCupcakeRequest;
use App\Http\Resources\CupcakeResource;
use App\Models\Cupcake;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CupcakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return CupcakeResource::collection(Cupcake::paginate(5));
        
        
        // ordre prix

        // nom

        // 
        
        // return Cupcake::query()->when($request->search, function (Builder $builder) use ($request) {
        //     $builder->where()
        // });

    }


    /**
     * Display a single resource.
     */
    public function show(int $id) {
        // return response()->json(Cupcake::findOrFail($id));
        return Cupcake::findOrFail($id);
        // return Cupcake::findOr($id, function () {
        //     abort(404, 'Oops...Not found!');
        // });
    }




    public function destroy(int $id) {
        $cupcake = Cupcake::find($id);
        if(!$cupcake) {
            return response()->json(["message" => "Ooops not found"], 404);
        }
        return $cupcake->destroy();
    }
    


    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Artist $artist)
    // {
    //     //
    // }



     /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCupcakeRequest $request)
    {
        
        $validated = $request->validated();

        $cupcake = new Cupcake;
        $cupcake->name = $validated["name"];
        $cupcake->price_in_cents = $validated["price_in_cents"];
        $cupcake->photo_url = $validated["photo_url"];
        $cupcake->description = $validated["description"];
        $cupcake->quantity = $validated["quantity"];
        $cupcake->is_available = $validated["is_available"];
        $cupcake->is_advertised = $validated["is_advertised"];


        return $cupcake->save();

        
    }

}
