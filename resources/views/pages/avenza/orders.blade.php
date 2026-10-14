@extends('layouts.app')
@section('title', 'My Orders')
@section('content')

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">ORDER HISTORY</div>
        <h2>My orders</h2>
        <p class="avz-head__sub">
            {{ $data->total() }} order{{ $data->total() == 1 ? '' : 's' }} placed with Avenza
        </p>
    </div>
    <div>
        <a href="{{ url('/avenza/shop') }}" class="btn btn-white">Keep shopping</a>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeIn">

        <div class="mb-3">
            <a href="{{ url('/avenza/orders') }}"
               class="avz-chip {{ request('status') ? '' : 'avz-chip--on' }}">All</a>
            @foreach(['pending', 'paid', 'shipped', 'cancelled'] as $s)
                <a href="{{ url('/avenza/orders?status='.$s) }}"
                   class="avz-chip {{ request('status') == $s ? 'avz-chip--on' : '' }}">{{ ucfirst($s) }}</a>
            @endforeach
        </div>

        @if($data->isEmpty())
            <div class="ibox">
                <div class="ibox-content">
                    <div class="avz-empty">
                        <i class="fa fa-archive"></i>
                        @if(request('status'))
                            <p>No {{ request('status') }} orders.</p>
                            <a href="{{ url('/avenza/orders') }}" class="btn btn-white btn-sm mt-2">See all orders</a>
                        @else
                            <p>You haven't ordered anything yet.</p>
                            <a href="{{ url('/avenza/shop') }}" class="btn btn-primary btn-sm mt-2">
                                Browse the catalogue
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            @foreach($data as $o)
                @php $pics = $thumbs[$o->order_id] ?? []; @endphp

                <div class="avz-order">
                    @if(count($pics))
                        <div class="avz-order__thumbs">
                            @foreach(array_slice($pics, 0, 3) as $photo)
                                <img src="{{ asset('_uploads/'.$photo) }}" alt="">
                            @endforeach
                            @if(count($pics) > 3)
                                <div class="avz-order__more">+{{ count($pics) - 3 }}</div>
                            @endif
                        </div>
                    @endif

                    <div class="avz-order__body">
                        <div class="avz-order__no">Order #{{ $o->order_id }}</div>
                        <div class="avz-order__meta">
                            {{ \Carbon\Carbon::parse($o->order_date)->format('j F Y') }}
                            &middot; {{ $o->item_count ?? 0 }} item{{ ($o->item_count ?? 0) == 1 ? '' : 's' }}
                            &middot; {{ $o->shipper_name ?? 'Courier not assigned' }}
                        </div>
                        <div class="mt-2">
                            <span class="avz-pill avz-pill--{{ $o->status }}">{{ ucfirst($o->status) }}</span>
                        </div>
                    </div>

                    <div class="avz-order__right">
                        <div class="price" style="font-size:19px;">
                            &#8369;{{ number_format($o->total ?? 0, 2) }}
                        </div>
                        <a href="{{ url('/avenza/orders/'.$o->order_id) }}" class="btn btn-white btn-sm mt-2">
                            View details
                        </a>
                    </div>
                </div>
            @endforeach

            {{ $data->links() }}
        @endif

    </div>
</div>
@endsection