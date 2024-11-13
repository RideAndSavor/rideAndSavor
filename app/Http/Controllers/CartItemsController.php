<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Contracts\LocationInterface;

class CartItemsController extends Controller
{
    public function __construct(private LocationInterface $interface)
    {

    }
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(CartItems $cartItem)
    {
        try {
            DB::beginTransaction();
            $this->interface->delete('CartItems', $cartItem->id);
            $cart = Cart::where('id', $cartItem->cart->id)->first();
            if ($cart->cartItems->count() === 0) {
                $this->interface->delete('Cart', $cart->id);
            }
            DB::commit();
            return response('no content', 204);
        } catch (\Exception $exception) {
            DB::rollBack();
            // return throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
