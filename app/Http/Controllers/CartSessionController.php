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
    $unit_price = $product->original_price;
    $quantity = $request->quantity ?? 1;
    $shop_id = $product->shop_id; // Assuming Product has a `shop_id` column

    // Get the first image of the product (assuming you store the image path in `upload_url`)
    $image = $product->images->first() ? $product->images->first()->upload_url : null;

    // Calculate discount (example: percentage discount)
    $discountPercentage = $product->discount_price ?? 0; // Percentage discount from request
    $discountAmount = ($unit_price * $quantity) * ($discountPercentage / 100); // Calculate discount amount
    $afterDiscountPrice = ($unit_price * $quantity) - $discountAmount; // Final price after discount

    // Check if item already exists in cart
    $existingItem = Cart::get($product->id);

    if ($existingItem) {
        // If item exists, update quantity, total price, and apply discount
        $newQuantity = $existingItem->quantity + $quantity;  // Add new quantity to existing quantity
        $newTotalPrice = $unit_price * $newQuantity;  // Recalculate total price with updated quantity
        $newDiscountAmount = ($unit_price * $newQuantity) * ($discountPercentage / 100);  // Recalculate discount
        $newAfterDiscountPrice = ($unit_price * $newQuantity) - $newDiscountAmount;  // Recalculate price after discount

        // Update the cart item
        Cart::update($product->id, [
            'quantity' => $quantity,  // Update quantity with new total
            'price' => $unit_price,  // Price remains the same
            'attributes' => [
                'user_id' => Auth::id(),
                'unit_price' => $unit_price,
                'total_price' => $newTotalPrice,  // Update total price with new total
                'discount_amount' => $newDiscountAmount,  // Update discount amount
                'after_discount_price' => $newAfterDiscountPrice,  // Update price after discount
                'shop_id' => $shop_id,
                'image' => $image,  // Store image URL in cart
            ]
        ]);
    } else {
        // If item does not exist, add as a new cart item
        Cart::add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $unit_price,
            'quantity' => $quantity,  // Add new quantity
            'attributes' => [
                'user_id' => Auth::id(),
                'unit_price' => $unit_price,
                'total_price' => $unit_price * $quantity,  // Initial total price
                'discount_amount' => $discountAmount,  // Store discount amount
                'after_discount_price' => $afterDiscountPrice,  // Store price after discount
                'shop_id' => $shop_id,
                'image' => $image,  // Store image URL in cart
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
    $discountPercentage = $product->discount_price ?? 0; // Get discount percentage

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
