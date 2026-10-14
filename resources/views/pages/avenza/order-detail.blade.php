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
        <div class="avz-head__eyebrow">ORDER HISTORY</div>
        <h2>Order #{{ $order->order_id }}</h2>
        <p class="avz-head__sub">
            Placed {{ \Carbon\Carbon::parse($order->order_date)->format('j F Y') }}
        </p>
    </div>
    <div>
        <span class="avz-pill avz-pill--{{ $order->status }}">{{ ucfirst($order->status) }}</span>
        <a href="{{ url('/avenza/orders') }}" class="btn btn-white">All orders</a>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeIn">
        <div class="row">

            <div class="col-lg-8">
                <div class="avz-receipt">

                    <div class="avz-receipt__head">
                        <div>
                            <div class="avz-receipt__mark">Avenza</div>
                            <div class="text-muted" style="font-size:12px;">Furniture Shop</div>
                        </div>
                        <div class="text-right">
                            <div class="avz-receipt__no">#{{ $order->order_id }}</div>
                            <div class="text-muted" style="font-size:12px;">
                                {{ \Carbon\Carbon::parse($order->order_date)->format('j M Y') }}
                            </div>
                        </div>
                    </div>

                    @if($order->status === 'cancelled')
                        <div class="avz-address avz-address--warn mt-3 mb-3">
                            This order was cancelled. Nothing will be delivered and the
                            items have gone back into stock.
                        </div>
                    @else
                        <ul class="avz-track mt-4 mb-4">
                            <li class="{{ $at >= 1 ? 'done' : '' }}">PLACED</li>
                            <li class="{{ $at >= 2 ? 'done' : '' }}">PAID</li>
                            <li class="{{ $at >= 3 ? 'done' : '' }}">SHIPPED</li>
                        </ul>
                    @endif

                    @foreach($lines as $l)
                        <div class="avz-line">
                            <div class="avz-line__media">
                                @if($l->photo)
                                    <img src="{{ asset('_uploads/'.$l->photo) }}" alt="">
                                @else
                                    <div class="avz-line__media--empty"><i class="fa fa-picture-o"></i></div>
                                @endif
                            </div>
                            <div class="avz-line__body">
                                <div class="avz-line__room">{{ strtoupper($l->category_name ?? 'AVENZA') }}</div>
                                <div class="avz-line__name">{{ $l->product_name }}</div>
                                <div class="text-muted" style="font-size:13px;">
                                    {{ $l->quantity }} &times; &#8369;{{ number_format($l->unit_price, 2) }}
                                    @if($l->unit) / {{ $l->unit }} @endif
                                </div>
                            </div>
                            <div class="avz-line__right">
                                <span class="price" style="font-size:17px;">
                                    &#8369;{{ number_format($l->quantity * $l->unit_price, 2) }}
                                </span>
                            </div>
                        </div>
                    @endforeach

                    <div class="avz-receipt__total">
                        <span>Order total</span>
                        <span class="price price-lg">&#8369;{{ number_format($total, 2) }}</span>
                    </div>

                    <p class="text-muted mt-3">
                        <small>Prices shown are what you paid at the time of ordering.</small>
                    </p>

                </div>
            </div>

            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="avz-rule">Delivery</div>

                        <div class="avz-address">
                            <strong>{{ $customer->customer_name }}</strong><br>
                            {{ $customer->contact_name ?: '' }}<br>
                            {{ $customer->address ?: 'No address on file' }}<br>
                            {{ $customer->city }} {{ $customer->postal_code }}<br>
                            {{ $customer->country }}
                        </div>

                        <dl class="avz-dl">
                            <dt>Courier</dt>
                            <dd>
                                {{ $order->shipper_name ?? 'Not assigned yet' }}
                                @if($order->shipper_phone)
                                    <div class="text-muted">{{ $order->shipper_phone }}</div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                @if($order->status === 'pending')
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="avz-rule">Changed your mind?</div>
                            <p class="text-muted" style="font-size:13px;">
                                This order hasn't been processed yet, so you can still cancel it.
                            </p>
                            <form action="{{ url('/avenza/orders/'.$order->order_id.'/cancel') }}" method="post"
                                  onsubmit="return confirm('Cancel order #{{ $order->order_id }}?')">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-white btn-block btn-sm" type="submit">Cancel this order</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection