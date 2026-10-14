<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function home()
    {
        $categories = DB::table('categories')
            ->leftJoin('products', 'categories.category_id', '=', 'products.category_id')
            ->select('categories.*', DB::raw('COUNT(products.product_id) as product_count'))
            ->groupBy('categories.category_id', 'categories.category_name', 'categories.slug',
                      'categories.description', 'categories.created_at', 'categories.updated_at')
            ->orderBy('categories.category_name')
            ->get();

        $featured = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->select('products.*', 'categories.category_name')
            ->where('products.stock', '>', 0)
            ->orderByDesc('products.product_id')
            ->limit(8)
            ->get();

        return view('pages.avenza.shop.home', compact('categories', 'featured'));
    }

    public function index(Request $request)
    {
        $query = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->select('products.*', 'categories.category_name', 'categories.slug');

        if ($q = $request->input('q')) {
            $query->where('products.product_name', 'like', '%' . $q . '%');
        }

        if ($cat = $request->input('category')) {
            $query->where('categories.slug', $cat);
        }

        match ($request->input('sort')) {
            'price_low'  => $query->orderBy('products.price'),
            'price_high' => $query->orderByDesc('products.price'),
            'newest'     => $query->orderByDesc('products.product_id'),
            default      => $query->orderBy('products.product_name'),
        };

        $data       = $query->paginate(12)->withQueryString();
        $categories = DB::table('categories')->orderBy('category_name')->get();

        // Name the current room for the page heading
        $room = $request->input('category')
            ? optional($categories->firstWhere('slug', $request->input('category')))->category_name
            : null;

        return view('pages.avenza.shop.browse', compact('data', 'categories', 'room'));
    }

    public function show($id)
    {
        $product = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.supplier_id')
            ->select('products.*', 'categories.category_name', 'categories.slug',
                     'suppliers.supplier_name')
            ->where('products.product_id', $id)
            ->first();

        if (! $product) {
            return redirect(url('/avenza/shop'))->with('error', 'That item is no longer listed.');
        }

        $related = DB::table('products')
            ->where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->where('stock', '>', 0)
            ->limit(4)
            ->get();

        return view('pages.avenza.shop.product', compact('product', 'related'));
    }
}