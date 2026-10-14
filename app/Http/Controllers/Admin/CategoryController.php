<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('categories')
            ->leftJoin('products', 'categories.category_id', '=', 'products.category_id')
            ->select('categories.*', DB::raw('COUNT(products.product_id) as product_count'))
            ->groupBy('categories.category_id', 'categories.category_name', 'categories.slug',
                      'categories.description', 'categories.created_at', 'categories.updated_at');

        if ($q = $request->input('q')) {
            $query->where('categories.category_name', 'like', '%' . $q . '%');
        }

        $data = $query->orderBy('categories.category_name')->paginate(12)->withQueryString();

        return view('pages.avenza.admin.categories.index', compact('data'));
    }

    public function show_add_form()
    {
        $category = null;
        return view('pages.avenza.admin.categories.form', compact('category'));
    }

    public function do_add(Request $request)
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
        ]);

        $data['slug'] = Str::slug($data['category_name']);

        if (DB::table('categories')->where('slug', $data['slug'])->exists()) {
            return back()->withInput()->with('error', 'A category with that name already exists.');
        }

        DB::table('categories')->insert($data + ['created_at' => now(), 'updated_at' => now()]);

        return redirect(url('/avenza/admin/categories'))->with('success', 'Category added.');
    }

    public function show_edit_form($id)
    {
        $category = DB::table('categories')->where('category_id', $id)->first();

        if (! $category) {
            return redirect(url('/avenza/admin/categories'))->with('error', 'Category not found.');
        }

        return view('pages.avenza.admin.categories.form', compact('category'));
    }

    public function do_update(Request $request)
    {
        $id = $request->input('category_id');

        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
        ]);

        $data['slug'] = Str::slug($data['category_name']);

        $clash = DB::table('categories')
            ->where('slug', $data['slug'])
            ->where('category_id', '!=', $id)
            ->exists();

        if ($clash) {
            return back()->withInput()->with('error', 'Another category already uses that name.');
        }

        DB::table('categories')->where('category_id', $id)
            ->update($data + ['updated_at' => now()]);

        return redirect(url('/avenza/admin/categories'))->with('success', 'Category updated.');
    }

    public function do_delete($id)
    {
        if (DB::table('products')->where('category_id', $id)->exists()) {
            return back()->with('error', 'That category still has products. Move them first.');
        }

        DB::table('categories')->where('category_id', $id)->delete();

        return back()->with('success', 'Category deleted.');
    }
}