@extends('layouts.app')
@section('title', 'Manage Customer')
@section('content')

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">AVENZA ADMIN</div>
        <h2>Manage Customer</h2>
        <p class="avz-head__sub">{{ $data->total() }} registered account{{ $data->total() == 1 ? '' : 's' }}</p>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="ibox">
            <div class="ibox-content">

                <form action="{{ url('/avenza/admin/customers') }}" method="get" class="row">
                    <div class="col-md-6 mb-2">
                        <input type="text" name="q" class="form-control"
                               placeholder="Search by customer name..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-primary btn-block" type="submit">Search</button>
                    </div>
                    <input type="hidden" name="status" value="{{ request('status') }}">
                </form>

                <div class="mt-2 mb-3">
                    <a href="{{ url('/avenza/admin/customers') }}"
                       class="avz-chip {{ request('status') ? '' : 'avz-chip--on' }}">All</a>
                    @foreach(['active', 'suspended', 'blocked'] as $s)
                        <a href="{{ url('/avenza/admin/customers?status='.$s) }}"
                           class="avz-chip {{ request('status') == $s ? 'avz-chip--on' : '' }}">{{ ucfirst($s) }}</a>
                    @endforeach
                </div>

                @if($data->isEmpty())
                    <div class="avz-empty">
                        <i class="fa fa-users"></i>
                        No customers match that filter.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Delivers to</th>
                                    <th class="text-center">Orders</th>
                                    <th style="width:250px;">Account status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $c)
                                <tr>
                                    <td>
                                        <strong>{{ $c->customer_name }}</strong>
                                        <div class="text-muted" style="font-size:12px;">{{ $c->email ?? 'no email on file' }}</div>
                                    </td>
                                    <td class="text-muted">{{ $c->contact_name ?: '—' }}</td>
                                    <td>
                                        @if($c->address || $c->city)
                                            {{ $c->address }}
                                            <div class="text-muted" style="font-size:12px;">
                                                {{ $c->city }} {{ $c->postal_code }}
                                            </div>
                                        @else
                                            <span class="text-muted">No address yet</span>
                                        @endif
                                    </td>
                                    <td class="text-center"><strong>{{ $c->order_count }}</strong></td>
                                    <td>
                                        <div class="mb-1">
                                            <span class="avz-pill avz-pill--{{ $c->status }}">{{ ucfirst($c->status) }}</span>
                                        </div>
                                        <form action="{{ url('/avenza/admin/customers/'.$c->customer_id.'/status') }}"
                                              method="post" class="d-flex">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-control form-control-sm mr-1">
                                                @foreach(['active', 'suspended', 'blocked'] as $s)
                                                    <option value="{{ $s }}" {{ $c->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $data->links() }}
                @endif

                <p class="text-muted mt-2">
                    <small>Suspended and blocked customers can still sign in and browse, but are stopped at checkout.</small>
                </p>

            </div>
        </div>
    </div>
</div>
@endsection