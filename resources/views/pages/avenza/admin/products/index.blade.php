@extends('layouts.app')
@section('title', 'Manage Product')
@section('content')

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">AVENZA ADMIN</div>
        <h2>Manage Product</h2>
        <p class="avz-head__sub">{{ $data->total() }} item{{ $data->total() == 1 ? '' : 's' }} in the catalogue</p>
    </div>
    <div>
        <a href="{{ url('/avenza/admin/products/create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add product
        </a>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">
        <div class="ibox">
            <div class="ibox-content">

                <form action="{{ url('/avenza/admin/products') }}" method="get" class="row">
                    <div class="col-md-6 mb-2">
                        <input type="text" name="q" class="form-control"
                               placeholder="Search by product name..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <button class="btn btn-primary btn-block" type="submit">Search</button>
                    </div>
                    @if(request('q') || request('category'))
                    <div class="col-md-3 mb-2">
                        <a href="{{ url('/avenza/admin/products') }}" class="btn btn-white btn-block">Clear filters</a>
                    </div>
                    @endif
                    <input type="hidden" name="category" value="{{ request('category') }}">
                </form>

                <div class="mt-2 mb-3">
                    <a href="{{ url('/avenza/admin/products'.(request('q') ? '?q='.request('q') : '')) }}"
                       class="avz-chip {{ request('category') ? '' : 'avz-chip--on' }}">All rooms</a>
                    @foreach($categories as $c)
                        <a href="{{ url('/avenza/admin/products?category='.$c->slug.(request('q') ? '&q='.request('q') : '')) }}"
                           class="avz-chip {{ request('category') == $c->slug ? 'avz-chip--on' : '' }}">{{ $c->category_name }}</a>
                    @endforeach
                </div>

                @if($data->isEmpty())
                    <div class="avz-empty">
                        <i class="fa fa-cube"></i>
                        @if(request('q') || request('category'))
                            Nothing matched that search.
                            <div class="mt-3">
                                <a href="{{ url('/avenza/admin/products') }}" class="btn btn-white btn-sm">Clear filters</a>
                            </div>
                        @else
                            The catalogue is empty.
                            <div class="mt-3">
                                <a href="{{ url('/avenza/admin/products/create') }}" class="btn btn-primary btn-sm">Add your first product</a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width:66px;"></th>
                                    <th>Product</th>
                                    <th>Supplier</th>
                                    <th class="text-right">Price</th>
                                    <th style="width:110px;">Stock</th>
                                    <th style="width:110px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $p)
                                <tr>
                                    <td>
                                        @if($p->photo)
                                            <img src="{{ asset('_uploads/'.$p->photo) }}" alt="" class="product-thumb-sm">
                                        @else
                                            <div class="product-thumb-sm text-center"
                                                 style="background:var(--stone);line-height:55px;color:var(--line);">
                                                <i class="fa fa-picture-o"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $p->product_name }}</strong>
                                        <div class="text-muted" style="font-size:12px;">
                                            {{ $p->category_name ?? 'Uncategorised' }}
                                            @if($p->unit) &middot; {{ $p->unit }} @endif
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $p->supplier_name ?? '—' }}</td>
                                    <td class="text-right">
                                        <span class="price">&#8369;{{ number_format($p->price, 2) }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $p->stock }}</strong>
                                        <span class="text-muted" style="font-size:11px;">
                                            @if($p->stock == 0) out of stock
                                            @elseif($p->stock <= 5) running low
                                            @endif
                                        </span>
                                        <div class="avz-meter">
                                            <div class="avz-meter__fill {{ $p->stock == 0 ? 'avz-meter__fill--none' : ($p->stock <= 5 ? 'avz-meter__fill--low' : '') }}"
                                                 style="width: {{ min(100, $p->stock * 5) }}%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ url('/avenza/admin/products/'.$p->product_id.'/edit') }}"
                                           class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>

                                        <form action="{{ url('/avenza/admin/products/'.$p->product_id) }}"
                                              method="post" style="display:inline;"
                                              onsubmit="return confirm('Delete {{ addslashes($p->product_name) }}?')">
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