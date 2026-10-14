@extends('layouts.app')
@section('title', 'Suppliers')
@section('content')

<x-page-header pagetitle="Suppliers" class="bg-info"/>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="ibox">
            <div class="ibox-title">
                <h5>{{ $data->total() }} supplier{{ $data->total() == 1 ? '' : 's' }}</h5>
                <div class="ibox-tools">
                    <a href="{{ url('/avenza/admin/suppliers/create') }}" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> Add supplier
                    </a>
                </div>
            </div>
            <div class="ibox-content">

                <form action="{{ url('/avenza/admin/suppliers') }}" method="get" class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <input type="text" name="q" class="form-control"
                               placeholder="Search supplier name..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{ url('/avenza/admin/suppliers') }}" class="btn btn-white">Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Phone</th>
                                <th class="text-center">Products</th>
                                <th style="width:110px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $s)
                            <tr>
                                <td><strong>{{ $s->supplier_name }}</strong></td>
                                <td>{{ $s->contact_name ?: '—' }}</td>
                                <td>
                                    {{ $s->city ?: '—' }}
                                    @if($s->country)<br><small class="text-muted">{{ $s->country }}</small>@endif
                                </td>
                                <td>{{ $s->phone ?: '—' }}</td>
                                <td class="text-center">
                                    <span class="label label-primary">{{ $s->product_count }}</span>
                                </td>
                                <td>
                                    <a href="{{ url('/avenza/admin/suppliers/'.$s->supplier_id.'/edit') }}"
                                       class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>

                                    <form action="{{ url('/avenza/admin/suppliers/'.$s->supplier_id) }}"
                                          method="post" style="display:inline;"
                                          onsubmit="return confirm('Delete {{ addslashes($s->supplier_name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No suppliers yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $data->links() }}

            </div>
        </div>
    </div>
</div>
@endsection