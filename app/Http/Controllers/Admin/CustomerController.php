<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('customers')
            ->leftJoin('users', 'customers.user_id', '=', 'users.id')
            ->leftJoin('orders', 'customers.customer_id', '=', 'orders.customer_id')
            ->select('customers.*', 'users.email',
                     DB::raw('COUNT(orders.order_id) as order_count'))
            // MySQL strict mode requires every selected column in GROUP BY
            ->groupBy(
                'customers.customer_id', 'customers.user_id', 'customers.customer_name',
                'customers.contact_name', 'customers.address', 'customers.city',
                'customers.postal_code', 'customers.country', 'customers.status',
                'customers.created_at', 'customers.updated_at', 'users.email'
            );

        if ($q = $request->input('q')) {
            $query->where('customers.customer_name', 'like', '%' . $q . '%');
        }

        if ($status = $request->input('status')) {
            $query->where('customers.status', $status);
        }

        $data = $query->orderBy('customers.customer_name')->paginate(15)->withQueryString();

        return view('pages.avenza.admin.customers', compact('data'));
    }

    public function update_status(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:active,suspended,blocked'],
        ]);

        DB::table('customers')->where('customer_id', $id)->update([
            'status'     => $request->input('status'),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Customer status updated.');
    }
}