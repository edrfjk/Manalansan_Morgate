@extends('layouts.app')
@section('title', $shipper ? 'Edit Shipper' : 'Add Shipper')
@section('content')

@php $editing = (bool) $shipper; @endphp

<x-page-header pagetitle="{{ $editing ? 'Edit Shipper' : 'Add Shipper' }}" class="bg-warning"/>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-content">

                        <form action="{{ $editing ? url('/avenza/admin/shippers/update') : url('/avenza/admin/shippers') }}"
                              method="post">
                            @csrf
                            @if($editing)
                                <input type="hidden" name="shipper_id" value="{{ $shipper->shipper_id }}">
                            @endif

                            <div class="form-group mb-3">
                                <label for="shipper_name">Shipper name</label>
                                <input type="text" class="form-control" name="shipper_name" id="shipper_name"
                                       value="{{ old('shipper_name', $shipper->shipper_name ?? '') }}"
                                       placeholder="Avenza In-House Delivery" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone"
                                       value="{{ old('phone', $shipper->phone ?? '') }}"
                                       placeholder="0917-000-0000">
                            </div>

                            <hr>
                            <button class="btn btn-primary" type="submit">{{ $editing ? 'Save changes' : 'Add shipper' }}</button>
                            <a href="{{ url('/avenza/admin/shippers') }}" class="btn btn-secondary">Back</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection