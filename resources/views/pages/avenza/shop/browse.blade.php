@extends('layouts.app')
@section('title', $room ?? 'Browse Furniture')
@section('content')

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">CATALOGUE</div>
        <h2>{{ $room ?? 'All furniture' }}</h2>
        <p class="avz-head__sub">
            {{ $data->total() }} piece{{ $data->total() == 1 ? '' : 's' }}
            @if(request('q')) matching "{{ request('q') }}" @endif
        </p>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeIn">

        <div class="ibox">
            <div class="ibox-content">
                <form action="{{ url('/avenza/shop') }}" method="get" class="row">
                    <div class="col-md-6 mb-2">
                        <input type="text" name="q" class="form-control"
                               placeholder="Search for a table, a chair, a wardrobe..."
                               value="{{ request('q') }}">
                    </div>
                    <div class="col-md-4 mb-2">
                        <select name="sort" class="form-control">
                            <option value="">Sort by name</option>
                            <option value="newest"     {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest first</option>
                            <option value="price_low"  {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: low to high</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: high to low</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-primary btn-block" type="submit">Search</button>
                    </div>
                    <input type="hidden" name="category" value="{{ request('category') }}">
                </form>

                <div class="mt-2">
                    <a href="{{ url('/avenza/shop') }}"
                       class="avz-chip {{ request('category') ? '' : 'avz-chip--on' }}">Every room</a>
                    @foreach($categories as $c)
                        <a href="{{ url('/avenza/shop?category='.$c->slug) }}"
                           class="avz-chip {{ request('category') == $c->slug ? 'avz-chip--on' : '' }}">{{ $c->category_name }}</a>
                    @endforeach
                </div>
            </div>
        </div>

        @if($data->isEmpty())
            <div class="ibox">
                <div class="ibox-content">
                    <div class="avz-empty">
                        <i class="fa fa-search"></i>
                        Nothing matched that search.
                        <div class="mt-3">
                            <a href="{{ url('/avenza/shop') }}" class="btn btn-primary btn-sm">Browse everything</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($data as $p)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        @include('pages.avenza.shop.partials.card', ['p' => $p])
                    </div>
                @endforeach
            </div>

            {{ $data->links() }}
        @endif

    </div>
</div>
@endsection