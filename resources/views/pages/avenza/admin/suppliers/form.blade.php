@extends('layouts.app')
@section('title', $supplier ? 'Edit Supplier' : 'Add Supplier')
@section('content')

@php $editing = (bool) $supplier; @endphp

<x-page-header pagetitle="{{ $editing ? 'Edit Supplier' : 'Add Supplier' }}" class="bg-warning"/>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-content">

                        <form action="{{ $editing ? url('/avenza/admin/suppliers/update') : url('/avenza/admin/suppliers') }}"
                              method="post">
                            @csrf
                            @if($editing)
                                <input type="hidden" name="supplier_id" value="{{ $supplier->supplier_id }}">
                            @endif

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="supplier_name">Supplier name</label>
                                    <input type="text" class="form-control" name="supplier_name" id="supplier_name"
                                           value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}" required>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="contact_name">Contact person</label>
                                    <input type="text" class="form-control" name="contact_name" id="contact_name"
                                           value="{{ old('contact_name', $supplier->contact_name ?? '') }}">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" name="address" id="address"
                                       value="{{ old('address', $supplier->address ?? '') }}">
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control" name="city" id="city"
                                           value="{{ old('city', $supplier->city ?? '') }}">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="postal_code">Postal code</label>
                                    <input type="text" class="form-control" name="postal_code" id="postal_code"
                                           value="{{ old('postal_code', $supplier->postal_code ?? '') }}">
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control" name="country" id="country"
                                           value="{{ old('country', $supplier->country ?? 'Philippines') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="phone"
                                           value="{{ old('phone', $supplier->phone ?? '') }}"
                                           placeholder="0917-000-0000">
                                </div>
                            </div>

                            <hr>
                            <button class="btn btn-primary" type="submit">{{ $editing ? 'Save changes' : 'Add supplier' }}</button>
                            <a href="{{ url('/avenza/admin/suppliers') }}" class="btn btn-secondary">Back</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection