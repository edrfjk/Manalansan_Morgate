<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('suppliers')
            ->leftJoin('products', 'suppliers.supplier_id', '=', 'products.supplier_id')
            ->select('suppliers.*', DB::raw('COUNT(products.product_id) as product_count'))
            ->groupBy('suppliers.supplier_id', 'suppliers.supplier_name', 'suppliers.contact_name',
                      'suppliers.address', 'suppliers.city', 'suppliers.postal_code',
                      'suppliers.country', 'suppliers.phone',
                      'suppliers.created_at', 'suppliers.updated_at');

        if ($q = $request->input('q')) {
            $query->where('suppliers.supplier_name', 'like', '%' . $q . '%');
        }

        $data = $query->orderBy('suppliers.supplier_name')->paginate(12)->withQueryString();

        return view('pages.avenza.admin.suppliers.index', compact('data'));
    }

    public function show_add_form()
    {
        $supplier = null;
        return view('pages.avenza.admin.suppliers.form', compact('supplier'));
    }

    public function do_add(Request $request)
    {
        DB::table('suppliers')->insert(
            $this->validated($request) + ['created_at' => now(), 'updated_at' => now()]
        );

        return redirect(url('/avenza/admin/suppliers'))->with('success', 'Supplier added.');
    }

    public function show_edit_form($id)
    {
        $supplier = DB::table('suppliers')->where('supplier_id', $id)->first();

        if (! $supplier) {
            return redirect(url('/avenza/admin/suppliers'))->with('error', 'Supplier not found.');
        }

        return view('pages.avenza.admin.suppliers.form', compact('supplier'));
    }

    public function do_update(Request $request)
    {
        DB::table('suppliers')->where('supplier_id', $request->input('supplier_id'))
            ->update($this->validated($request) + ['updated_at' => now()]);

        return redirect(url('/avenza/admin/suppliers'))->with('success', 'Supplier updated.');
    }

    public function do_delete($id)
    {
        if (DB::table('products')->where('supplier_id', $id)->exists()) {
            return back()->with('error', 'That supplier still has products. Reassign them first.');
        }

        DB::table('suppliers')->where('supplier_id', $id)->delete();

        return back()->with('success', 'Supplier deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'contact_name'  => ['nullable', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:100'],
            'phone'         => ['nullable', 'string', 'max:40'],
        ]);
    }
}