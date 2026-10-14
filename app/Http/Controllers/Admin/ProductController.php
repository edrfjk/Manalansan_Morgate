<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.supplier_id')
            ->select(
                'products.*',
                'categories.category_name',
                'suppliers.supplier_name'
            );

        if ($q = $request->input('q')) {
            $query->where('products.product_name', 'like', '%' . $q . '%');
        }

        if ($cat = $request->input('category')) {
            $query->where('categories.slug', $cat);
        }

        $data = $query
            ->orderBy('products.product_name')
            ->paginate(12)
            ->withQueryString();

        $categories = DB::table('categories')
            ->orderBy('category_name')
            ->get();

        return view(
            'pages.avenza.admin.products.index',
            compact('data', 'categories')
        );
    }

    public function show_add_form()
    {
        $product = null;

        $categories = DB::table('categories')
            ->orderBy('category_name')
            ->get();

        $suppliers = DB::table('suppliers')
            ->orderBy('supplier_name')
            ->get();

        return view(
            'pages.avenza.admin.products.form',
            compact('product', 'categories', 'suppliers')
        );
    }

    public function do_add(Request $request)
    {
        $data = $this->validated($request);

        // Remove UploadedFile object from the database data
        unset($data['photo']);

        $filename = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            $filename =
                time() . '_' .
                Str::random(6) . '.' .
                $file->getClientOriginalExtension();

            $file->move(public_path('_uploads'), $filename);
        }

        DB::table('products')->insert(
            $data + [
                'photo'      => $filename,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect(url('/avenza/admin/products'))
            ->with('success', 'Product added.');
    }

    public function show_edit_form($id)
    {
        $product = DB::table('products')
            ->where('product_id', $id)
            ->first();

        if (! $product) {
            return redirect(url('/avenza/admin/products'))
                ->with('error', 'That product no longer exists.');
        }

        $categories = DB::table('categories')
            ->orderBy('category_name')
            ->get();

        $suppliers = DB::table('suppliers')
            ->orderBy('supplier_name')
            ->get();

        return view(
            'pages.avenza.admin.products.form',
            compact('product', 'categories', 'suppliers')
        );
    }

    public function do_update(Request $request)
    {
        $id = $request->input('product_id');

        $data = $this->validated($request);

        // Remove UploadedFile object from database update data
        unset($data['photo']);

        // Keep the old photo by default
        $filename = $request->input('old_photo');

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            $filename =
                time() . '_' .
                Str::random(6) . '.' .
                $file->getClientOriginalExtension();

            $file->move(public_path('_uploads'), $filename);
        }

        DB::table('products')
            ->where('product_id', $id)
            ->update(
                $data + [
                    'photo'      => $filename,
                    'updated_at' => now(),
                ]
            );

        return redirect(url('/avenza/admin/products'))
            ->with('success', 'Product updated.');
    }

    public function do_delete($id)
    {
        if (
            DB::table('order_details')
                ->where('product_id', $id)
                ->exists()
        ) {
            return back()->with(
                'error',
                'That product appears on past orders and cannot be deleted. Set its stock to 0 instead.'
            );
        }

        DB::table('products')
            ->where('product_id', $id)
            ->delete();

        return back()->with('success', 'Product deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],

            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,category_id'
            ],

            'supplier_id' => [
                'nullable',
                'integer',
                'exists:suppliers,supplier_id'
            ],

            'unit' => [
                'nullable',
                'string',
                'max:100'
            ],

            'price' => [
                'required',
                'numeric',
                'min:0'
            ],

            'stock' => [
                'required',
                'integer',
                'min:0'
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ]);
    }
}