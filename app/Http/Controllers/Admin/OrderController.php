<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
            ->leftJoin('shippers', 'orders.shipper_id', '=', 'shippers.shipper_id')
            ->leftJoin('order_details', 'orders.order_id', '=', 'order_details.order_id')
            ->select('orders.*', 'customers.customer_name', 'shippers.shipper_name',
                     DB::raw('SUM(order_details.quantity) as item_count'),
                     DB::raw('SUM(order_details.quantity * order_details.unit_price) as total'))
            ->groupBy(
                'orders.order_id', 'orders.customer_id', 'orders.employee_id',
                'orders.order_date', 'orders.shipper_id', 'orders.status',
                'orders.created_at', 'orders.updated_at',
                'customers.customer_name', 'shippers.shipper_name'
            );

        if ($status = $request->input('status')) {
            $query->where('orders.status', $status);
        }

        $data = $query->orderByDesc('orders.order_id')->paginate(15)->withQueryString();

        return view('pages.avenza.admin.orders.index', compact('data'));
    }

    public function show($id)
    {
        $order = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
            ->leftJoin('shippers', 'orders.shipper_id', '=', 'shippers.shipper_id')
            ->select('orders.*', 'customers.customer_name', 'customers.contact_name',
                     'customers.address', 'customers.city', 'customers.postal_code',
                     'customers.country', 'shippers.shipper_name')
            ->where('orders.order_id', $id)
            ->first();

        if (! $order) {
            return redirect(url('/avenza/admin/orders'))->with('error', 'Order not found.');
        }

        $lines = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.product_id')
            ->select('order_details.*', 'products.product_name', 'products.unit', 'products.photo')
            ->where('order_details.order_id', $id)
            ->get();

        $shippers = DB::table('shippers')->orderBy('shipper_name')->get();

        return view('pages.avenza.admin.orders.show', compact('order', 'lines', 'shippers'));
    }

    public function do_update(Request $request, $id)
    {
        $data = $request->validate([
            'status'     => ['required', 'in:pending,paid,shipped,cancelled'],
            'shipper_id' => ['nullable', 'integer', 'exists:shippers,shipper_id'],
        ]);

        DB::table('orders')->where('order_id', $id)->update([
            'status'      => $data['status'],
            'shipper_id'  => $data['shipper_id'] ?? null,
            'employee_id' => auth()->id(),   // who processed it
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'Order updated.');
    }

    // "Delete cart products" in the WBS
    public function delete_line($orderId, $lineId)
    {
        $line = DB::table('order_details')
            ->where('order_detail_id', $lineId)
            ->where('order_id', $orderId)
            ->first();

        if ($line) {
            DB::transaction(function () use ($line, $lineId) {
                DB::table('products')->where('product_id', $line->product_id)
                    ->increment('stock', $line->quantity);

                DB::table('order_details')->where('order_detail_id', $lineId)->delete();
            });
        }

        return back()->with('success', 'Item removed and stock returned.');
    }

    public function do_delete($id)
    {
        DB::transaction(function () use ($id) {
            $lines = DB::table('order_details')->where('order_id', $id)->get();

            foreach ($lines as $line) {
                DB::table('products')->where('product_id', $line->product_id)
                    ->increment('stock', $line->quantity);
            }

            DB::table('order_details')->where('order_id', $id)->delete();
            DB::table('orders')->where('order_id', $id)->delete();
        });

        return redirect(url('/avenza/admin/orders'))
            ->with('success', 'Order deleted and stock returned.');
    }
}