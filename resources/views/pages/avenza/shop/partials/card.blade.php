<div class="avz-card">
    <a href="{{ url('/avenza/shop/'.$p->product_id) }}" class="avz-card__media">
        @if($p->photo)
            <img src="{{ asset('_uploads/'.$p->photo) }}" alt="{{ $p->product_name }}">
        @else
            <div class="avz-card__media--empty"><i class="fa fa-picture-o"></i></div>
        @endif

        @if($p->stock == 0)
            <span class="avz-card__flag avz-card__flag--out">SOLD OUT</span>
        @elseif($p->stock <= 5)
            <span class="avz-card__flag">ONLY {{ $p->stock }} LEFT</span>
        @endif
    </a>

    <div class="avz-card__body">
        <div class="avz-card__room">{{ strtoupper($p->category_name ?? 'AVENZA') }}</div>
        <div class="avz-card__name">
            <a href="{{ url('/avenza/shop/'.$p->product_id) }}">{{ $p->product_name }}</a>
        </div>

        <div class="avz-card__foot">
            <div class="mb-2">
                <span class="price" style="font-size:19px;">&#8369;{{ number_format($p->price, 2) }}</span>
                @if($p->unit)
                    <small class="text-muted"> / {{ $p->unit }}</small>
                @endif
            </div>

            @if($p->stock > 0)
                <form action="{{ url('/avenza/cart') }}" method="post">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->product_id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-primary btn-block btn-sm" type="submit">Add to cart</button>
                </form>
            @else
                <button class="btn btn-white btn-block btn-sm" disabled>Sold out</button>
            @endif
        </div>
    </div>
</div>