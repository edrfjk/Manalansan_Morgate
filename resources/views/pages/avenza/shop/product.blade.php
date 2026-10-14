@extends('layouts.app')
@section('title', $product->product_name)
@section('content')

<div class="wrapper wrapper-content">
    <div class="animated fadeIn avz-detail">

        <div class="ibox">
            <div class="ibox-content" style="padding:34px;">
                <div class="row">

                    <div class="col-md-6 avz-detail__media">
                        @if($product->photo)
                            <img src="{{ asset('_uploads/'.$product->photo) }}" alt="{{ $product->product_name }}">
                        @else
                            <div class="avz-card__media--empty" style="height:380px;line-height:380px;border:1px solid var(--line);">
                                <i class="fa fa-picture-o"></i>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <div class="avz-crumb">
                            <a href="{{ url('/avenza/shop') }}">Catalogue</a>
                            @if($product->category_name)
                                <span class="mx-1">/</span>
                                <a href="{{ url('/avenza/shop?category='.$product->slug) }}">{{ $product->category_name }}</a>
                            @endif
                        </div>

                        <h1>{{ $product->product_name }}</h1>

                        <p class="avz-detail__lede">
                            {{ $product->description ?: 'No description has been written for this piece yet.' }}
                        </p>

                        <div class="avz-detail__price">
                            <span class="price price-lg">&#8369;{{ number_format($product->price, 2) }}</span>
                            @if($product->unit)
                                <span class="text-muted"> / {{ $product->unit }}</span>
                            @endif
                            <div class="mt-2">
                                @if($product->stock > 5)
                                    <span class="avz-pill avz-pill--active">In stock</span>
                                @elseif($product->stock > 0)
                                    <span class="avz-pill avz-pill--pending">Only {{ $product->stock }} left</span>
                                @else
                                    <span class="avz-pill avz-pill--cancelled">Sold out</span>
                                @endif
                            </div>
                        </div>

                        @if($product->stock > 0)
                            <form action="{{ url('/avenza/cart') }}" method="post" class="row">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                                <div class="col-4 col-sm-3 mb-2">
                                    <input type="number" name="quantity" class="form-control"
                                           value="1" min="1" max="{{ $product->stock }}">
                                </div>
                                <div class="col-8 col-sm-6 mb-2">
                                    <button class="btn btn-primary btn-block" type="submit">
                                        <i class="fa fa-shopping-cart"></i> Add to cart
                                    </button>
                                </div>
                            </form>
                        @else
                            <p class="text-muted">This piece is out of stock. It may be restocked — check back.</p>
                        @endif

                        <dl class="avz-dl mt-4">
                            <dt>Room</dt>
                            <dd>{{ $product->category_name ?? 'Uncategorised' }}</dd>

                            <dt>Made by</dt>
                            <dd>{{ $product->supplier_name ?? 'Avenza' }}</dd>

                            <dt>Delivery</dt>
                            <dd>Arranged at checkout. Courier assigned once your order is confirmed.</dd>
                        </dl>

                        <a href="{{ url('/avenza/shop') }}" class="btn btn-white mt-3">Back to catalogue</a>
                    </div>

                </div>
            </div>
        </div>

        @if($related->count())
            <div class="avz-section">
                <h3>Goes with this</h3>
                @if($product->category_name)
                    <a href="{{ url('/avenza/shop?category='.$product->slug) }}" class="btn btn-white btn-sm">
                        All {{ $product->category_name }}
                    </a>
                @endif
            </div>

            <div class="row">
                @foreach($related as $r)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        @include('pages.avenza.shop.partials.card', [
                            'p' => (object) array_merge((array) $r, ['category_name' => $product->category_name])
                        ])
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection