<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartSessionController extends Controller
{


    public function addToCart(Request $request)
    {
        $product = Product::with('images')->findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        // 🔹 Check if the product has enough stock
        if ($product->stock_quantity < $quantity) {
            return response()->json(['error' => 'Not enough stock available.'], 200);
        }

        $unit_price = $product->original_price;
        $shop_id = $product->shop_id;
        $image = $product->images->first() ? $product->images->first()->upload_url : null;

        $discountPercentage = $product->discount_percent ?? 0;
        $discountAmount = ($unit_price * $quantity) * ($discountPercentage / 100);
        $afterDiscountPrice = ($unit_price * $quantity) - $discountAmount;

        $existingItem = Cart::get($product->id);

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;

            // 🔹 Check if adding the new quantity exceeds available stock
            if ($product->stock_quantity < $newQuantity) {
                return response()->json(['error' => 'Not enough stock available for this quantity.'], 400);
            }

            $newTotalPrice = $unit_price * $newQuantity;
            $newDiscountAmount = ($unit_price * $newQuantity) * ($discountPercentage / 100);
            $newAfterDiscountPrice = ($unit_price * $newQuantity) - $newDiscountAmount;

            Cart::update($product->id, [
                'quantity' => $newQuantity,
                'price' => $unit_price,
                'attributes' => [
                    'user_id' => Auth::id(),
                    'unit_price' => $unit_price,
                    'total_price' => $newTotalPrice,
                    'discount_amount' => $newDiscountAmount,
                    'after_discount_price' => $newAfterDiscountPrice,
                    'shop_id' => $shop_id,
                    'image' => $image,
                ]
            ]);
        } else {
            Cart::add([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $unit_price,
                'quantity' => $quantity,
                'attributes' => [
                    'user_id' => Auth::id(),
                    'unit_price' => $unit_price,
                    'total_price' => $unit_price * $quantity,
                    'discount_amount' => $discountAmount,
                    'after_discount_price' => $afterDiscountPrice,
                    'shop_id' => $shop_id,
                    'image' => $image,
                ]
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully!']);
    }


public function updateCartItem(Request $request)
{
    $productId = $request->product_id;
    $operation = $request->operation; // "increase" or "decrease"


    // Get existing cart item
    $cartItem = Cart::get($productId);

    if (!$cartItem) {
        return response()->json(['message' => 'Item not found in cart'], 404);
    }

    $product = Product::findOrFail($productId);
    $unit_price = $product->original_price;
    $discountPercentage = $product->discount_percent ?? 0; // Get discount percentage

    // Calculate new quantity
    $newQuantity = ($operation === 'increase') ? $cartItem->quantity + 1 : $cartItem->quantity - 1;
    // dd($newQuantity);

    if ($newQuantity <= 0) {
        // Remove item if quantity becomes 0
        Cart::remove($productId);
        return response()->json(['message' => 'Item removed from cart']);
    }

    // Recalculate prices
    $newTotalPrice = $unit_price * $newQuantity;
    $newDiscountAmount = ($newTotalPrice) * ($discountPercentage / 100);
    $newAfterDiscountPrice = $newTotalPrice - $newDiscountAmount;

    // Update cart item
    Cart::update($productId, [
        'quantity' => ['value' => $newQuantity, 'relative' => false], // ✅ Set exact quantity,
        'attributes' => [
            'user_id' => Auth::id(),
            'unit_price' => $unit_price,
            'total_price' => $newTotalPrice,
            'discount_amount' => $newDiscountAmount,
            'after_discount_price' => $newAfterDiscountPrice,
            'shop_id' => $product->shop_id,
            'image' => $cartItem->attributes['image'] ?? null, // Keep existing image
        ]
    ]);

    return response()->json(['message' => 'Cart item updated successfully', 'new_quantity' => $newQuantity]);
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
