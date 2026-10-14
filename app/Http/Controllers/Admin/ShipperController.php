<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipperController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('shippers')
            ->leftJoin('orders', 'shippers.shipper_id', '=', 'orders.shipper_id')
            ->select('shippers.*', DB::raw('COUNT(orders.order_id) as order_count'))
            ->groupBy('shippers.shipper_id', 'shippers.shipper_name', 'shippers.phone',
                      'shippers.created_at', 'shippers.updated_at');

        if ($q = $request->input('q')) {
            $query->where('shippers.shipper_name', 'like', '%' . $q . '%');
        }

        $data = $query->orderBy('shippers.shipper_name')->paginate(12)->withQueryString();

        return view('pages.avenza.admin.shippers.index', compact('data'));
    }

    public function show_add_form()
    {
        $shipper = null;
        return view('pages.avenza.admin.shippers.form', compact('shipper'));
    }

    public function do_add(Request $request)
    {
        DB::table('shippers')->insert(
            $this->validated($request) + ['created_at' => now(), 'updated_at' => now()]
        );

        return redirect(url('/avenza/admin/shippers'))->with('success', 'Shipper added.');
    }

    public function show_edit_form($id)
    {
        $shipper = DB::table('shippers')->where('shipper_id', $id)->first();

        if (! $shipper) {
            return redirect(url('/avenza/admin/shippers'))->with('error', 'Shipper not found.');
        }

        return view('pages.avenza.admin.shippers.form', compact('shipper'));
    }

    public function do_update(Request $request)
    {
        DB::table('shippers')->where('shipper_id', $request->input('shipper_id'))
            ->update($this->validated($request) + ['updated_at' => now()]);

        return redirect(url('/avenza/admin/shippers'))->with('success', 'Shipper updated.');
    }

    public function do_delete($id)
    {
        if (DB::table('orders')->where('shipper_id', $id)->exists()) {
            return back()->with('error', 'That shipper is attached to existing orders.');
        }

        DB::table('shippers')->where('shipper_id', $id)->delete();

        return back()->with('success', 'Shipper deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'shipper_name' => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:40'],
        ]);
    }
}