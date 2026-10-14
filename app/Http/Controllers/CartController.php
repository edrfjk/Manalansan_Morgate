<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        [$lines, $total] = $this->cartLines();

        return view('pages.avenza.cart', compact('lines', 'total'));
    }

    public function do_add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,product_id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = DB::table('products')->where('product_id', $data['product_id'])->first();

        $cart    = (array) session('cart', []);
        $current = $cart[$data['product_id']] ?? 0;
        $wanted  = $current + $data['quantity'];

        if ($wanted > $product->stock) {
            return back()->with('error',
                'Only ' . $product->stock . ' left of ' . $product->product_name . '.');
        }

        $cart[$data['product_id']] = $wanted;
        session(['cart' => $cart]);

        return back()->with('success', $product->product_name . ' added to your cart.');
    }

    public function do_update(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity'   => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $cart = (array) session('cart', []);

        if ($data['quantity'] === 0) {
            unset($cart[$data['product_id']]);
        } else {
            $product = DB::table('products')->where('product_id', $data['product_id'])->first();

            if ($product && $data['quantity'] > $product->stock) {
                return back()->with('error', 'Only ' . $product->stock . ' left in stock.');
            }

            $cart[$data['product_id']] = $data['quantity'];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Cart updated.');
    }

    public function do_remove($productId)
    {
        $cart = (array) session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item removed.');
    }

    public function checkout()
    {
        [$lines, $total] = $this->cartLines();

        if (empty($lines)) {
            return redirect(url('/avenza/shop'))->with('error', 'Your cart is empty.');
        }

        $customer = $this->customer();
        $shippers = DB::table('shippers')->orderBy('shipper_name')->get();

        return view('pages.avenza.checkout', compact('lines', 'total', 'customer', 'shippers'));
    }

    public function do_place_order(Request $request)
    {
        $request->validate([
            'shipper_id' => ['nullable', 'integer', 'exists:shippers,shipper_id'],
        ]);

        $customer = $this->customer();

        if (! $customer) {
            return redirect(url('/avenza/profile'))
                ->with('error', 'Add your delivery details before checking out.');
        }

        if ($customer->status !== 'active') {
            return redirect(url('/avenza/cart'))
                ->with('error', 'Your account cannot place orders right now. Please contact the shop.');
        }

        [$lines, $total] = $this->cartLines();

        if (empty($lines)) {
            return redirect(url('/avenza/shop'))->with('error', 'Your cart is empty.');
        }

        try {
            $orderId = DB::transaction(function () use ($lines, $customer, $request) {

                $orderId = DB::table('orders')->insertGetId([
                    'customer_id' => $customer->customer_id,
                    'order_date'  => now()->toDateString(),
                    'shipper_id'  => $request->input('shipper_id') ?: null,
                    'status'      => 'pending',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                foreach ($lines as $line) {
                    // Lock the row and re-check. Two customers can both reach
                    // checkout holding the last item; without this, both
                    // succeed and the shop oversells.
                    $fresh = DB::table('products')
                        ->where('product_id', $line->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (! $fresh || $fresh->stock < $line->quantity) {
                        throw new \RuntimeException(
                            'Not enough stock left for ' . $line->product_name . '.'
                        );
                    }

                    DB::table('order_details')->insert([
                        'order_id'   => $orderId,
                        'product_id' => $line->product_id,
                        'quantity'   => $line->quantity,
                        'unit_price' => $line->price,   // frozen at checkout
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('products')
                        ->where('product_id', $line->product_id)
                        ->decrement('stock', $line->quantity);
                }

                return $orderId;
            });
        } catch (\RuntimeException $e) {
            // Transaction rolled back — no order, no stock movement
            return redirect(url('/avenza/cart'))->with('error', $e->getMessage());
        }

        session()->forget('cart');

        return redirect(url('/avenza/orders/' . $orderId))
            ->with('success', 'Order #' . $orderId . ' placed. Thank you.');
    }

    /*
    | Cart lives in the session as [product_id => qty]. Joining it against live
    | product rows means a product deleted since it was added just drops out.
    */
    private function cartLines(): array
    {
        $cart = (array) session('cart', []);

        if (empty($cart)) {
            return [[], 0];
        }

        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->select('products.*', 'categories.category_name')
            ->whereIn('products.product_id', array_keys($cart))
            ->get();

        $lines = [];
        $total = 0;

        foreach ($products as $product) {
            $qty = (int) $cart[$product->product_id];

            $product->quantity   = $qty;
            $product->line_total = $qty * $product->price;

            $total  += $product->line_total;
            $lines[] = $product;
        }

        return [$lines, $total];
    }

    private function customer()
    {
        return DB::table('customers')->where('user_id', auth()->id())->first();
    }
}