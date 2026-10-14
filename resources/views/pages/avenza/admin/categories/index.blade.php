@extends('layouts.app')
@section('title', 'Categories')
@section('content')

<x-page-header pagetitle="Categories" class="bg-info"/>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="ibox">
            <div class="ibox-title">
                <h5>{{ $data->total() }} categor{{ $data->total() == 1 ? 'y' : 'ies' }}</h5>
                <div class="ibox-tools">
                    <a href="{{ url('/avenza/admin/categories/create') }}" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> Add category
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                <form action="{{ url('/avenza/admin/categories') }}" method="get" class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <input type="text" name="q" class="form-control"
                               placeholder="Search category name..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{ url('/avenza/admin/categories') }}" class="btn btn-white">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th class="text-center">Products</th>
                                <th style="width:110px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $c)
                            <tr>
                                <td><strong>{{ $c->category_name }}</strong></td>
                                <td><code>{{ $c->slug }}</code></td>
                                <td>{{ $c->description ?: '—' }}</td>
                                <td class="text-center">
                                    <span class="label label-primary">{{ $c->product_count }}</span>
                                </td>
                                <td>
                                    <a href="{{ url('/avenza/admin/categories/'.$c->category_id.'/edit') }}"
                                       class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>

                                    <form action="{{ url('/avenza/admin/categories/'.$c->category_id) }}"
                                          method="post" style="display:inline;"
                                          onsubmit="return confirm('Delete {{ addslashes($c->category_name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No categories yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $data->links() }}

                <p class="text-muted mt-2">
                    <small>A category with products attached can't be deleted — move the products first.</small>
                </p>

            </div>
        </div>
    </div>
</div>
@endsection