@extends('layouts.app')
@section('title', $category ? 'Edit Category' : 'Add Category')
@section('content')

@php $editing = (bool) $category; @endphp

<x-page-header pagetitle="{{ $editing ? 'Edit Category' : 'Add Category' }}" class="bg-warning"/>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="row">
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-content">

                        <form action="{{ $editing ? url('/avenza/admin/categories/update') : url('/avenza/admin/categories') }}"
                              method="post">
                            @csrf
                            @if($editing)
                                <input type="hidden" name="category_id" value="{{ $category->category_id }}">
                            @endif

                            <div class="form-group mb-3">
                                <label for="category_name">Category name</label>
                                <input type="text" class="form-control" name="category_name" id="category_name"
                                       value="{{ old('category_name', $category->category_name ?? '') }}"
                                       placeholder="Living Room" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="3"
                                          placeholder="Sofas, coffee tables, TV racks">{{ old('description', $category->description ?? '') }}</textarea>
                            </div>

                            @if($editing)
                                <p class="text-muted">
                                    <small>Current slug: <code>{{ $category->slug }}</code>.
                                    Renaming the category regenerates it, which changes the
                                    <code>?category=</code> links customers may have bookmarked.</small>
                                </p>
                            @endif

                            <hr>
                            <button class="btn btn-primary" type="submit">{{ $editing ? 'Save changes' : 'Add category' }}</button>
                            <a href="{{ url('/avenza/admin/categories') }}" class="btn btn-secondary">Back</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection