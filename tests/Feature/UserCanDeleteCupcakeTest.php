<?php

use App\Models\Cupcake;
use App\Models\User;
use Illuminate\Http\Request;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;



test('admin user can delete a cupcake', function () {

    // add a cupcake to delete it after
    $cupcake = Cupcake::factory()->create();

    // dd($cupcake);

    // create an admin user
    $adminUser = User::factory()->create(["is_admin" => true]);


    // logged this user
        // delete the cupcake
    $response = actingAs($adminUser)
        ->deleteJson(route("cupcake.delete", ["id" => $cupcake->id]), []);

    // 
    $response->assertStatus(200);

    // check if previous id cupcake not existing anymore
    expect(Cupcake::find($cupcake->id))->toBeNull();

});




test('logged in user can not delete a cupcake', function () {

    // add a cupcake to delete it after
    $cupcake = Cupcake::factory()->create();


    // create an admin user
    $AuthenticatedUser = User::factory()->create(["is_admin" => false]);


    // logged this user
        // delete the cupcake
    actingAs($AuthenticatedUser)
        ->deleteJson(route("cupcake.delete", ["id" => $cupcake->id]), [])->assertStatus(401);

    // check if previous id cupcake is still existing 
    expect(Cupcake::get())->toHaveCount(1);

    // expect(Cupcake::find($cupcake->id))->toBeInstanceOf(Cupcake::class);

});





test('anonymous user cannot deleted a cupcake', function () {
    // add a cupcake to delete it after
    $cupcake = Cupcake::factory()->create();


    // logged this user
        // delete the cupcake
    deleteJson(route("cupcake.delete", ["id" => $cupcake->id]), [])->assertStatus(401);

    // check if previous id cupcake is still existing 
    expect(Cupcake::get())->toHaveCount(1);

});
