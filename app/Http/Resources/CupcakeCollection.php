<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CupcakeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($cupcake) {
                return [
                    "id" => $cupcake->id,
                    "name" => $cupcake->name,
                    "price_in_cents" => $cupcake->price_in_cents,
                    "photo_url" => $cupcake->photo_url,
                    "quantity" => $cupcake->quantity,
                    "categories" => $cupcake->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name
                        ];
                    }),
                ];
            })
        ];
    }


    public function with($request)
    {
        return [
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }



}
