<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartSessionController extends Controller
{
//     public function addToCart(Request $request)
// {
//     $product = Product::findOrFail($request->product_id);
//     $unit_price = $product->original_price;
//     $quantity = $request->quantity ?? 1;
//     $shop_id = $product->shop_id; // Assuming Product has a `shop_id` column

//     // Check if item already exists in cart
//     $existingItem = Cart::get($product->id);

//     if ($existingItem) {
//         // If item exists, update quantity and total price
//         $newQuantity = $existingItem->quantity + $quantity;
//         $newTotalPrice = $unit_price * $newQuantity;

//         Cart::update($product->id, [
//             'quantity' => $quantity,
//             'attributes' => [
//                 'user_id' => Auth::id(),
//                 'unit_price' => $unit_price,
//                 'total_price' => $newTotalPrice, // Update total price
//                 'shop_id' => $shop_id,
//             ]
//         ]);
//     } else {
//         // If item does not exist, add as a new cart item
//         Cart::add([
//             'id' => $product->id,
//             'name' => $product->name,
//             'price' => $unit_price,
//             'quantity' => $quantity,
//             'attributes' => [
//                 'user_id' => Auth::id(),
//                 'unit_price' => $unit_price,
//                 'total_price' => $unit_price * $quantity, // Initial total price
//                 'shop_id' => $shop_id,
//             ]
//         ]);
//     }

//     return response()->json(['message' => 'Product added to cart successfully!']);
// }

public function addToCart(Request $request)
{
    $product = Product::with('images')->findOrFail($request->product_id);
    $unit_price = $product->original_price;
    $quantity = $request->quantity ?? 1;
    $shop_id = $product->shop_id; // Assuming Product has a `shop_id` column

    // Get the first image of the product (assuming you store the image path in `url`)
    $image = $product->images->first() ? $product->images->first()->upload_url : null;

    // Check if item already exists in cart
    $existingItem = Cart::get($product->id);

    if ($existingItem) {
        // If item exists, update quantity and total price
        $newQuantity = $existingItem->quantity + $quantity;
        $newTotalPrice = $unit_price * $newQuantity;

        Cart::update($product->id, [
            'quantity' => $quantity,
            'attributes' => [
                'user_id' => Auth::id(),
                'unit_price' => $unit_price,
                'total_price' => $newTotalPrice, // Update total price
                'shop_id' => $shop_id,
                'image' => $image, // Store image URL in cart
            ]
        ]);
    } else {
        // If item does not exist, add as a new cart item
        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $unit_price,
            'quantity' => $quantity,
            'attributes' => [
                'user_id' => Auth::id(),
                'unit_price' => $unit_price,
                'total_price' => $unit_price * $quantity, // Initial total price
                'shop_id' => $shop_id,
                'image' => $image, // Store image URL in cart
            ]
        ]);
    }

    return response()->json(['message' => 'Product added to cart successfully!']);
}



    public function getCartItems()
    {
        $cartItems = Cart::getContent();
        return response()->json($cartItems);
    }

    public function removeCartItem($id)
    {

        Cart::remove($id);
        return response()->json(['message' => 'Item removed from cart']);
    }

    public function clearCart()
    {
        Cart::clear();
        return response()->json(['message' => 'Cart cleared']);
    }
}
