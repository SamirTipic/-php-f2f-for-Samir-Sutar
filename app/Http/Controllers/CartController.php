<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * View the user's cart.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function view()
    {
        $user = auth('api')->user();

        $cart = Cart::with(['items.product'])
                    ->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Cart is empty',
                'items' => [],
                'total_amount' => 0
            ]);
        }

        // Recalculate total just in case
        $cart->total_amount = $cart->items->sum('line_total');

        return response()->json($cart);
    }

    /**
     * Add item to cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        // Validate request
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth('api')->user();
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

       
        $product = Product::find($productId);
    

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['total_amount' => 0] // Initial total
        );

        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $productId)
                            ->first();

        $currentQuantity = $cartItem ? $cartItem->quantity : 0;
        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > $product->stock) {
            return response()->json([
                'error' => 'Insufficient stock',
                'message' => "Only {$product->stock} items available in stock.",
                'available_stock' => $product->stock
            ], 400);
        }

        if ($cartItem) {
           
            $cartItem->quantity = $newQuantity;
            $cartItem->line_total = $cartItem->quantity * $cartItem->unit_price;
            $cartItem->save();
        } else {
          
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'user_id' => $user->id, 
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'line_total' => $quantity * $product->price,
            ]);
        }


        return response()->json([
            'status' => 'success',
            'item' => $cartItem
        ]);
    }
}
