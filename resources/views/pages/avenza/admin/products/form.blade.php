@extends('layouts.app')
@section('title', $product ? 'Edit Product' : 'Add Product')
@section('content')

@php $editing = (bool) $product; @endphp

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">MANAGE PRODUCT</div>
        <h2>{{ $editing ? $product->product_name : 'Add a product' }}</h2>
        <p class="avz-head__sub">
            {{ $editing ? 'Editing an existing catalogue item' : 'New item for the Avenza catalogue' }}
        </p>
    </div>
    <div>
        <a href="{{ url('/avenza/admin/products') }}" class="btn btn-white">Back to catalogue</a>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">

        <form action="{{ $editing ? url('/avenza/admin/products/update') : url('/avenza/admin/products') }}"
              method="post" enctype="multipart/form-data">
            @csrf
            @if($editing)
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                <input type="hidden" name="old_photo" value="{{ $product->photo }}">
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="ibox">
                        <div class="ibox-content">

                            <div class="avz-rule">Details</div>

                            <div class="form-group mb-3">
                                <label for="product_name">Product name</label>
                                <input type="text" class="form-control" name="product_name" id="product_name"
                                       value="{{ old('product_name', $product->product_name ?? '') }}"
                                       placeholder="Narra Dining Table, 6-seater" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4"
                                          placeholder="Materials, dimensions, finish, care instructions.">{{ old('description', $product->description ?? '') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="category_id">Room</label>
                                    <select class="form-control" name="category_id" id="category_id">
                                        <option value="">— uncategorised —</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->category_id }}"
                                                {{ old('category_id', $product->category_id ?? '') == $c->category_id ? 'selected' : '' }}>
                                                {{ $c->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="supplier_id">Supplier</label>
                                    <select class="form-control" name="supplier_id" id="supplier_id">
                                        <option value="">— none —</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->supplier_id }}"
                                                {{ old('supplier_id', $product->supplier_id ?? '') == $s->supplier_id ? 'selected' : '' }}>
                                                {{ $s->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="avz-rule mt-4">Pricing and stock</div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label for="price">Price (&#8369;)</label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                           name="price" id="price"
                                           value="{{ old('price', $product->price ?? '') }}" required>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="unit">Sold as</label>
                                    <input type="text" class="form-control" name="unit" id="unit"
                                           placeholder="per piece, 1 set"
                                           value="{{ old('unit', $product->unit ?? '') }}">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="stock">Stock on hand</label>
                                    <input type="number" min="0" class="form-control" name="stock" id="stock"
                                           value="{{ old('stock', $product->stock ?? 0) }}" required>
                                </div>
                            </div>

                            @if($editing)
                                <p class="text-muted">
                                    <small>Changing the price affects new orders only. Past orders keep
                                    the price the customer actually paid.</small>
                                </p>
                            @endif

                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="avz-rule">Photo</div>

                            @if($editing && $product->photo)
                                <img src="{{ asset('_uploads/'.$product->photo) }}" alt=""
                                     class="product-thumb mb-3">
                            @else
                                <div class="product-thumb-empty mb-3"><i class="fa fa-picture-o"></i></div>
                            @endif

                            <input type="file" class="form-control" name="photo" id="photo" accept="image/*">
                            <p class="text-muted mt-2">
                                <small>
                                    {{ $editing ? 'Leave empty to keep the current photo.' : 'Optional, but the catalogue looks bare without one.' }}
                                    Landscape crops best.
                                </small>
                            </p>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-content">
                            <button class="btn btn-primary btn-block" type="submit">
                                {{ $editing ? 'Save changes' : 'Add to catalogue' }}
                            </button>
                            <a href="{{ url('/avenza/admin/products') }}" class="btn btn-secondary btn-block">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection