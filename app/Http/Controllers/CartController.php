<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItems;
use Illuminate\Http\Request;
use App\Http\Requests\CartRequest;
use Illuminate\Support\Facades\DB;
use App\Contracts\LocationInterface;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(private LocationInterface $cartInterface)
    {

    }
    public function index()
    {
        $cartWithItems = Cart::query()->where('user_id', Auth::id())->with(['cartItems.food', 'cartItems.restaurant'])
            ->latest('updated_at')->first(); //only return the cart object where latest updated_at with cartItems.food and cartItems.restaurant
        return new CartResource($cartWithItems);
    }

    public function store(CartRequest $request)
    {
        $validatedData = $request->validated();
        // dd(count($validatedData['cart_item']));
        try {
            DB::beginTransaction();
            $validatedData['cart']['user_id'] = Auth::id();
            $cart = $this->cartInterface->store('Cart', $validatedData['cart']);
            foreach ($validatedData['cart_item'] as $cartItem) {
                $cartItem['cart_id'] = $cart->id;
                $this->cartInterface->store('CartItems', $cartItem);
            }
            DB::commit();
            $cartWithItems = Cart::query()->where('id', '=', $cart->id)->with(['cartItems.food', 'cartItems.restaurant'])->first();

            return new CartResource($cartWithItems);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }

    }

    public function show(Cart $cart): void
    {

    }

    public function update(CartRequest $request, Cart $cart)
    {
        $validatedData = $request->validated();
        // dd($cartItemIDs[0]);
        try {
            DB::beginTransaction();
            $validatedData['cart']['user_id'] = Auth::id();
            $cart = $this->cartInterface->update('Cart', $validatedData['cart'], $cart->id);
            foreach ($validatedData['cart_item'] as $index => $cartItem) {
                $cartItem['cart_id'] = $cart->id;
                $cartItemIDs = CartItems::query()->where('cart_id', '=', $cart->id)->pluck('id')->toArray();
                $this->cartInterface->update('CartItems', $cartItem, $cartItemIDs[$index]);
            }
            DB::commit();
            $cartWithItems = Cart::query()->where('id', '=', $cart->id)->with(['cartItems.food', 'cartItems.restaurant'])->first();
            return new CartResource($cartWithItems);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }

    public function destroy(Cart $cart)
    {
    }
}
