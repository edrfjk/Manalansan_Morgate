@extends('layouts.app')
@section('title', 'My Cart')
@section('content')

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">YOUR SELECTION</div>
        <h2>My cart</h2>
        <p class="avz-head__sub">
            {{ count($lines) }} {{ count($lines) == 1 ? 'piece' : 'pieces' }} held for you
        </p>
    </div>
    <div>
        <a href="{{ url('/avenza/shop') }}" class="btn btn-white">Keep shopping</a>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeIn">

        @if(empty($lines))
            <div class="ibox">
                <div class="ibox-content">
                    <div class="avz-empty">
                        <i class="fa fa-shopping-cart"></i>
                        <p>Your cart is empty.</p>
                        <a href="{{ url('/avenza/shop') }}" class="btn btn-primary btn-sm mt-2">
                            Browse the catalogue
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="row">

                <div class="col-lg-8">
                    <div class="ibox">
                        <div class="ibox-content">
                            @foreach($lines as $l)
                                <div class="avz-line">
                                    <div class="avz-line__media">
                                        <a href="{{ url('/avenza/shop/'.$l->product_id) }}">
                                            @if($l->photo)
                                                <img src="{{ asset('_uploads/'.$l->photo) }}" alt="{{ $l->product_name }}">
                                            @else
                                                <div class="avz-line__media--empty"><i class="fa fa-picture-o"></i></div>
                                            @endif
                                        </a>
                                    </div>

                                    <div class="avz-line__body">
                                        <div class="avz-line__room">{{ strtoupper($l->category_name ?? 'AVENZA') }}</div>
                                        <div class="avz-line__name">
                                            <a href="{{ url('/avenza/shop/'.$l->product_id) }}">{{ $l->product_name }}</a>
                                        </div>
                                        <div class="text-muted" style="font-size:13px;">
                                            &#8369;{{ number_format($l->price, 2) }}
                                            @if($l->unit) / {{ $l->unit }} @endif
                                        </div>

                                        @if($l->quantity > $l->stock)
                                            <div class="mt-2">
                                                <span class="avz-pill avz-pill--cancelled">
                                                    Only {{ $l->stock }} left — reduce the quantity
                                                </span>
                                            </div>
                                        @endif

                                        <form action="{{ url('/avenza/cart/update') }}" method="post" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="product_id" value="{{ $l->product_id }}">
                                            <input type="number" name="quantity" class="form-control form-control-sm avz-qty"
                                                   value="{{ $l->quantity }}" min="1" max="{{ $l->stock }}">
                                            <button class="btn btn-white btn-sm" type="submit">Update</button>

                                            <a href="#" class="btn btn-white btn-sm"
                                               onclick="event.preventDefault(); document.getElementById('rm-{{ $l->product_id }}').submit();">
                                                Remove
                                            </a>
                                        </form>

                                        <form id="rm-{{ $l->product_id }}"
                                              action="{{ url('/avenza/cart/'.$l->product_id) }}"
                                              method="post" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>

                                    <div class="avz-line__right">
                                        <span class="price" style="font-size:18px;">
                                            &#8369;{{ number_format($l->line_total, 2) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="avz-summary">
                        <div class="avz-summary__row">
                            <span>Items</span>
                            <span>{{ collect($lines)->sum('quantity') }}</span>
                        </div>
                        <div class="avz-summary__row">
                            <span>Delivery</span>
                            <span>Arranged after order</span>
                        </div>

                        <div class="avz-summary__total">
                            <span>Total</span>
                            <strong>&#8369;{{ number_format($total, 2) }}</strong>
                        </div>

                        <a href="{{ url('/avenza/checkout') }}" class="btn btn-primary btn-block">
                            Proceed to checkout
                        </a>

                        <p class="avz-summary__note">
                            Nothing is charged yet. You'll confirm delivery details on the next screen.
                        </p>
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
@endsection