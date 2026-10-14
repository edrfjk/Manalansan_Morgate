<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'products'       => DB::table('products')->count(),
            'customers'      => DB::table('customers')->count(),
            'pending_orders' => DB::table('orders')->where('status', 'pending')->count(),
            'low_stock'      => DB::table('products')->where('stock', '<=', 5)->count(),
        ];

        $revenue = DB::table('orders')
            ->join('order_details', 'orders.order_id', '=', 'order_details.order_id')
            ->whereIn('orders.status', ['paid', 'shipped'])
            ->sum(DB::raw('order_details.quantity * order_details.unit_price'));

        /* ---- Chart 1: revenue, last 14 days ------------------------------
         | Query only returns days that had sales, so build the full range in
         | PHP and fill the gaps — otherwise the line skips empty days and
         | misreads as continuous trade.
         */
        $from = Carbon::today()->subDays(13);

        $sales = DB::table('orders')
            ->join('order_details', 'orders.order_id', '=', 'order_details.order_id')
            ->whereIn('orders.status', ['paid', 'shipped'])
            ->where('orders.order_date', '>=', $from->toDateString())
            ->select('orders.order_date',
                     DB::raw('SUM(order_details.quantity * order_details.unit_price) as day_total'))
            ->groupBy('orders.order_date')
            ->pluck('day_total', 'order_date');

        $salesLabels = [];
        $salesValues = [];

        for ($d = $from->copy(); $d <= Carbon::today(); $d->addDay()) {
            $key = $d->toDateString();
            $salesLabels[] = $d->format('j M');
            $salesValues[] = round((float) ($sales[$key] ?? 0), 2);
        }

        /* ---- Chart 2: orders by status ---------------------------------- */
        $statusCounts = DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');

        $statusOrder  = ['pending', 'paid', 'shipped', 'cancelled'];
        $statusValues = array_map(fn ($s) => (int) ($statusCounts[$s] ?? 0), $statusOrder);

        /* ---- Chart 3: units sold per room -------------------------------- */
        $byRoom = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.product_id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->whereIn('orders.status', ['paid', 'shipped'])
            ->select(DB::raw('COALESCE(categories.category_name, "Uncategorised") as room'),
                     DB::raw('SUM(order_details.quantity) as units'))
            ->groupBy('room')
            ->orderByDesc('units')
            ->get();

        $roomLabels = $byRoom->pluck('room')->all();
        $roomValues = $byRoom->pluck('units')->map(fn ($v) => (int) $v)->all();

        /* ---- Tables ------------------------------------------------------ */
        $recent = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
            ->select('orders.*', 'customers.customer_name')
            ->orderByDesc('orders.order_id')
            ->limit(6)
            ->get();

        $lowStock = DB::table('products')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(6)
            ->get();

        $topSellers = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.product_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.order_id')
            ->whereIn('orders.status', ['paid', 'shipped'])
            ->select('products.product_id', 'products.product_name', 'products.photo',
                     DB::raw('SUM(order_details.quantity) as units'),
                     DB::raw('SUM(order_details.quantity * order_details.unit_price) as earned'))
            ->groupBy('products.product_id', 'products.product_name', 'products.photo')
            ->orderByDesc('units')
            ->limit(5)
            ->get();

        return view('pages.avenza.admin.dashboard', compact(
            'stats', 'revenue', 'recent', 'lowStock', 'topSellers',
            'salesLabels', 'salesValues', 'statusOrder', 'statusValues',
            'roomLabels', 'roomValues'
        ));
    }
}