@extends('layouts.app')
@section('title', 'Avenza Furniture Shop')
@section('content')

<div class="wrapper wrapper-content">
    <div class="animated fadeIn">

        <div class="avz-hero">
            <div class="avz-hero__eyebrow">AVENZA FURNITURE</div>
            <h1>Pieces made to outlast the room around them.</h1>
            <p>
                Solid hardwood, joined by hand, finished slowly. Browse by room or
                search for the one thing you came here for.
            </p>
            <a href="{{ url('/avenza/shop') }}" class="btn btn-primary">See the full catalogue</a>
        </div>

        <div class="avz-section">
            <h3>Shop by room</h3>
        </div>

        <div class="row">
            @foreach($categories as $c)
                <div class="col-md-3 col-sm-6">
                    <a href="{{ url('/avenza/shop?category='.$c->slug) }}" class="avz-room">
                        <div class="avz-room__name">{{ $c->category_name }}</div>
                        <div class="avz-room__desc">{{ $c->description }}</div>
                        <div class="avz-room__count">
                            {{ $c->product_count }} {{ $c->product_count == 1 ? 'PIECE' : 'PIECES' }}
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="avz-section">
            <h3>Just arrived</h3>
            <a href="{{ url('/avenza/shop?sort=newest') }}" class="btn btn-white btn-sm">See all new</a>
        </div>

        <div class="row">
            @forelse($featured as $p)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('pages.avenza.shop.partials.card', ['p' => $p])
                </div>
            @empty
                <div class="col-12">
                    <div class="avz-empty">
                        <i class="fa fa-cube"></i>
                        Nothing is in stock right now. Check back soon.
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection