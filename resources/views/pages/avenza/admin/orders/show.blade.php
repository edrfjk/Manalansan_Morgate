@extends('layouts.app')
@section('title', 'Order #'.$order->order_id)
@section('content')

@php
    $total = $lines->sum(fn ($l) => $l->quantity * $l->unit_price);
    $steps = ['pending' => 1, 'paid' => 2, 'shipped' => 3];
    $at    = $steps[$order->status] ?? 0;
@endphp

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">MANAGE CART OR ORDER</div>
        <h2>Order #{{ $order->order_id }}</h2>
        <p class="avz-head__sub">
            Placed {{ \Carbon\Carbon::parse($order->order_date)->format('j F Y') }}
            by {{ $order->customer_name }}
        </p>
    </div>
    <div>
        <span class="avz-pill avz-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
        <a href="{{ url('/avenza/admin/orders') }}" class="btn btn-white">Back to orders</a>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="row">

            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-content">

                        @if($order->status !== 'cancelled')
                            <ul class="avz-track mb-4">
                                <li class="{{ $at >= 1 ? 'done' : '' }}">PLACED</li>
                                <li class="{{ $at >= 2 ? 'done' : '' }}">PAID</li>
                                <li class="{{ $at >= 3 ? 'done' : '' }}">SHIPPED</li>
                            </ul>
                        @endif

                        <div class="avz-rule">Line items</div>

                        @if($lines->isEmpty())
                            <div class="avz-empty">
                                <i class="fa fa-shopping-basket"></i>
                                Every item has been removed from this order.
                            </div>
                        @else
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width:66px;"></th>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Unit price</th>
                                        <th class="text-right">Line total</th>
                                        <th style="width:44px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($lines as $l)
                                    <tr>
                                        <td>
                                            @if($l->photo)
                                                <img src="{{ asset('_uploads/'.$l->photo) }}" alt="" class="product-thumb-sm">
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $l->product_name }}</strong>
                                            <div class="text-muted" style="font-size:12px;">{{ $l->unit }}</div>
                                        </td>
                                        <td class="text-center">{{ $l->quantity }}</td>
                                        <td class="text-right text-muted">&#8369;{{ number_format($l->unit_price, 2) }}</td>
                                        <td class="text-right">
                                            <span class="price">&#8369;{{ number_format($l->quantity * $l->unit_price, 2) }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ url('/avenza/admin/orders/'.$order->order_id.'/lines/'.$l->order_detail_id) }}"
                                                  method="post"
                                                  onsubmit="return confirm('Remove this item? Stock will be returned.')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Order total</th>
                                        <th class="text-right">
                                            <span class="price price-lg">&#8369;{{ number_format($total, 2) }}</span>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <p class="text-muted">
                                <small>Unit prices are what the customer paid at checkout, not today's
                                catalogue price. Removing a line returns its quantity to stock.</small>
                            </p>
                        @endif

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="avz-rule">Deliver to</div>
                        <dl class="avz-dl">
                            <dt>Customer</dt>
                            <dd>{{ $order->customer_name }}</dd>

                            <dt>Contact</dt>
                            <dd>{{ $order->contact_name ?: 'Not provided' }}</dd>

                            <dt>Address</dt>
                            <dd>
                                {{ $order->address ?: 'No address on file' }}
                                @if($order->city)
                                    <div class="text-muted">{{ $order->city }} {{ $order->postal_code }}</div>
                                    <div class="text-muted">{{ $order->country }}</div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="ibox">
                    <div class="ibox-content">
                        <div class="avz-rule">Update</div>

                        <form action="{{ url('/avenza/admin/orders/'.$order->order_id.'/update') }}" method="post">
                            @csrf
                            @method('PATCH')

                            <div class="form-group mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    @foreach(['pending', 'paid', 'shipped', 'cancelled'] as $s)
                                        <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="shipper_id">Courier</label>
                                <select name="shipper_id" id="shipper_id" class="form-control">
                                    <option value="">— not assigned —</option>
                                    @foreach($shippers as $s)
                                        <option value="{{ $s->shipper_id }}" {{ $order->shipper_id == $s->shipper_id ? 'selected' : '' }}>
                                            {{ $s->shipper_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn btn-primary btn-block" type="submit">Save</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection