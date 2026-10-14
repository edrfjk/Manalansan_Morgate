@extends('layouts.app')
@section('title', 'Manage Cart or Order')
@section('content')

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">AVENZA ADMIN</div>
        <h2>Manage Cart or Order</h2>
        <p class="avz-head__sub">{{ $data->total() }} order{{ $data->total() == 1 ? '' : 's' }} on record</p>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="ibox">
            <div class="ibox-content">

                <div class="mb-3">
                    <a href="{{ url('/avenza/admin/orders') }}"
                       class="avz-chip {{ request('status') ? '' : 'avz-chip--on' }}">All orders</a>
                    @foreach(['pending', 'paid', 'shipped', 'cancelled'] as $s)
                        <a href="{{ url('/avenza/admin/orders?status='.$s) }}"
                           class="avz-chip {{ request('status') == $s ? 'avz-chip--on' : '' }}">{{ ucfirst($s) }}</a>
                    @endforeach
                </div>

                @if($data->isEmpty())
                    <div class="avz-empty">
                        <i class="fa fa-inbox"></i>
                        No orders match that filter.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-right">Total</th>
                                    <th>Courier</th>
                                    <th>Status</th>
                                    <th style="width:110px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $o)
                                <tr>
                                    <td>
                                        <strong>#{{ $o->order_id }}</strong>
                                        <div class="text-muted" style="font-size:12px;">
                                            {{ \Carbon\Carbon::parse($o->order_date)->format('j M Y') }}
                                        </div>
                                    </td>
                                    <td>{{ $o->customer_name }}</td>
                                    <td class="text-center">{{ $o->item_count ?? 0 }}</td>
                                    <td class="text-right">
                                        <span class="price">&#8369;{{ number_format($o->total ?? 0, 2) }}</span>
                                    </td>
                                    <td class="text-muted">{{ $o->shipper_name ?? 'Not assigned' }}</td>
                                    <td><span class="avz-pill avz-pill--{{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
                                    <td>
                                        <a href="{{ url('/avenza/admin/orders/'.$o->order_id) }}"
                                           class="btn btn-info btn-sm" title="Open"><i class="fa fa-eye"></i></a>

                                        <form action="{{ url('/avenza/admin/orders/'.$o->order_id) }}" method="post"
                                              style="display:inline;"
                                              onsubmit="return confirm('Delete order #{{ $o->order_id }}? Stock will be returned.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $data->links() }}
                @endif

            </div>
        </div>
    </div>
</div>
@endsection