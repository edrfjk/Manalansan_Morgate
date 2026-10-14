<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $customer = $this->customer();

        if (! $customer) {
            return redirect(url('/avenza/profile'))
                ->with('error', 'Add your details first.');
        }

        $query = DB::table('orders')
            ->leftJoin('shippers', 'orders.shipper_id', '=', 'shippers.shipper_id')
            ->leftJoin('order_details', 'orders.order_id', '=', 'order_details.order_id')
            ->select('orders.*', 'shippers.shipper_name',
                     DB::raw('SUM(order_details.quantity) as item_count'),
                     DB::raw('SUM(order_details.quantity * order_details.unit_price) as total'))
            ->where('orders.customer_id', $customer->customer_id)
            ->groupBy('orders.order_id', 'orders.customer_id', 'orders.employee_id',
                      'orders.order_date', 'orders.shipper_id', 'orders.status',
                      'orders.created_at', 'orders.updated_at', 'shippers.shipper_name');

        if ($status = $request->input('status')) {
            $query->where('orders.status', $status);
        }

        $data = $query->orderByDesc('orders.order_id')->paginate(10)->withQueryString();

        // Thumbnails so the list isn't a wall of order numbers
        $thumbs = [];
        if ($data->count()) {
            $rows = DB::table('order_details')
                ->join('products', 'order_details.product_id', '=', 'products.product_id')
                ->select('order_details.order_id', 'products.photo')
                ->whereIn('order_details.order_id', collect($data->items())->pluck('order_id'))
                ->whereNotNull('products.photo')
                ->get();

            foreach ($rows as $r) {
                $thumbs[$r->order_id][] = $r->photo;
            }
        }

        return view('pages.avenza.orders', compact('data', 'thumbs'));
    }

    public function show($id)
    {
        $customer = $this->customer();

        $order = DB::table('orders')
            ->leftJoin('shippers', 'orders.shipper_id', '=', 'shippers.shipper_id')
            ->select('orders.*', 'shippers.shipper_name', 'shippers.phone as shipper_phone')
            ->where('orders.order_id', $id)
            // Scoped to this customer — typing someone else's order number
            // into the URL returns 404, not their receipt.
            ->where('orders.customer_id', $customer->customer_id ?? 0)
            ->first();

        if (! $order) {
            abort(404);
        }

        $lines = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.product_id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->select('order_details.*', 'products.product_name', 'products.unit',
                     'products.photo', 'categories.category_name')
            ->where('order_details.order_id', $id)
            ->get();

        return view('pages.avenza.order-detail', compact('order', 'lines', 'customer'));
    }

    public function do_cancel($id)
    {
        $customer = $this->customer();

        $order = DB::table('orders')
            ->where('order_id', $id)
            ->where('customer_id', $customer->customer_id ?? 0)
            ->first();

        if (! $order || $order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        DB::transaction(function () use ($id) {
            $lines = DB::table('order_details')->where('order_id', $id)->get();

            foreach ($lines as $line) {
                DB::table('products')->where('product_id', $line->product_id)
                    ->increment('stock', $line->quantity);
            }

            DB::table('orders')->where('order_id', $id)->update([
                'status'     => 'cancelled',
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Order cancelled. The items are back in stock.');
    }

    public function edit()
    {
        $customer = $this->customer();

        return view('pages.avenza.profile', compact('customer'));
    }

    public function do_update(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'contact_name'  => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:100'],
        ]);

        $customer = $this->customer();

        if ($customer) {
            DB::table('customers')->where('customer_id', $customer->customer_id)
                ->update($data + ['updated_at' => now()]);
        } else {
            // Admin accounts have no customers row until they need one
            DB::table('customers')->insert($data + [
                'user_id'    => auth()->id(),
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('users')->where('id', auth()->id())
            ->update(['name' => $data['customer_name'], 'updated_at' => now()]);

        return back()->with('success', 'Profile saved.');
    }

    private function customer()
    {
        return DB::table('customers')->where('user_id', auth()->id())->first();
    }
}