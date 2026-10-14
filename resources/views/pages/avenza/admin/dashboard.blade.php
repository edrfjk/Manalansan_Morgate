@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')

<div class="avz-head">
    <div>
        <div class="avz-head__eyebrow">AVENZA ADMIN</div>
        <h2>Shop overview</h2>
        <p class="avz-head__sub">{{ now()->format('l, j F Y') }}</p>
    </div>
    <div>
        <a href="{{ url('/avenza/admin/orders?status=pending') }}" class="btn btn-white">
            Pending orders
        </a>
        <a href="{{ url('/avenza/admin/products/create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add product
        </a>
    </div>
</div>

<div class="wrapper wrapper-content">
    <div class="animated fadeInRightBig">

        {{-- Stat tiles --}}
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="avz-stat">
                    <div class="avz-stat__label">Products</div>
                    <div class="avz-stat__value">{{ $stats['products'] }}</div>
                    <div class="avz-stat__note">items listed</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="avz-stat avz-stat--walnut">
                    <div class="avz-stat__label">Customers</div>
                    <div class="avz-stat__value">{{ $stats['customers'] }}</div>
                    <div class="avz-stat__note">registered accounts</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="avz-stat avz-stat--brass">
                    <div class="avz-stat__label">Awaiting action</div>
                    <div class="avz-stat__value">{{ $stats['pending_orders'] }}</div>
                    <div class="avz-stat__note">pending orders</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="avz-stat avz-stat--brass">
                    <div class="avz-stat__label">Revenue</div>
                    <div class="avz-stat__value price">&#8369;{{ number_format($revenue, 0) }}</div>
                    <div class="avz-stat__note">paid and shipped</div>
                </div>
            </div>
        </div>

        {{-- Revenue trend + order mix --}}
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Revenue, last 14 days</h5>
                    </div>
                    <div class="ibox-content">
                        @if(array_sum($salesValues) == 0)
                            <div class="avz-empty">
                                <i class="fa fa-line-chart"></i>
                                No completed sales in this period yet. The chart fills in
                                once orders are marked paid or shipped.
                            </div>
                        @else
                            <div class="avz-chart"><canvas id="chartSales"></canvas></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Order mix</h5>
                    </div>
                    <div class="ibox-content">
                        @if(array_sum($statusValues) == 0)
                            <div class="avz-empty">
                                <i class="fa fa-pie-chart"></i>
                                No orders yet.
                            </div>
                        @else
                            <div class="avz-chart avz-chart--sm"><canvas id="chartStatus"></canvas></div>
                            <ul class="avz-legend">
                                @php
                                    $swatch = ['pending' => '#B8873F', 'paid' => '#2F4739',
                                               'shipped' => '#6B8F73', 'cancelled' => '#A5453B'];
                                @endphp
                                @foreach($statusOrder as $i => $s)
                                    <li>
                                        <span>
                                            <span class="avz-swatch" style="background:{{ $swatch[$s] }}"></span>
                                            {{ ucfirst($s) }}
                                        </span>
                                        <strong>{{ $statusValues[$i] }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Units per room + top sellers --}}
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Units sold by room</h5>
                    </div>
                    <div class="ibox-content">
                        @if(empty($roomValues))
                            <div class="avz-empty">
                                <i class="fa fa-bar-chart"></i>
                                Nothing sold yet.
                            </div>
                        @else
                            <div class="avz-chart avz-chart--sm"><canvas id="chartRooms"></canvas></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Best sellers</h5>
                    </div>
                    <div class="ibox-content">
                        @if($topSellers->isEmpty())
                            <div class="avz-empty">
                                <i class="fa fa-trophy"></i>
                                No sales to rank yet.
                            </div>
                        @else
                            @foreach($topSellers as $i => $p)
                                <div class="avz-rank">
                                    <div class="avz-rank__no">{{ $i + 1 }}</div>
                                    @if($p->photo)
                                        <img src="{{ asset('_uploads/'.$p->photo) }}" alt=""
                                             class="product-thumb-sm mr-3" style="width:40px;height:40px;">
                                    @endif
                                    <div class="avz-rank__body ml-2">
                                        <div class="avz-rank__name">{{ $p->product_name }}</div>
                                        <small class="text-muted">{{ $p->units }} sold</small>
                                    </div>
                                    <div class="price">&#8369;{{ number_format($p->earned, 0) }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent orders + restocking --}}
        <div class="row">
            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Recent orders</h5>
                        <div class="ibox-tools">
                            <a href="{{ url('/avenza/admin/orders') }}" class="btn btn-white btn-xs">View all</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        @if($recent->isEmpty())
                            <div class="avz-empty">
                                <i class="fa fa-inbox"></i>
                                No orders have come in yet.
                            </div>
                        @else
                            <table class="table table-hover">
                                <thead>
                                    <tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th></th></tr>
                                </thead>
                                <tbody>
                                @foreach($recent as $o)
                                    <tr>
                                        <td><strong>#{{ $o->order_id }}</strong></td>
                                        <td>{{ $o->customer_name }}</td>
                                        <td class="text-muted">{{ \Carbon\Carbon::parse($o->order_date)->format('j M') }}</td>
                                        <td><span class="avz-pill avz-pill--{{ $o->status }}">{{ ucfirst($o->status) }}</span></td>
                                        <td class="text-right">
                                            <a href="{{ url('/avenza/admin/orders/'.$o->order_id) }}"
                                               class="btn btn-white btn-xs">Open</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Needs restocking</h5>
                        <div class="ibox-tools">
                            <span class="avz-pill avz-pill--pending">{{ $stats['low_stock'] }}</span>
                        </div>
                    </div>
                    <div class="ibox-content">
                        @if($lowStock->isEmpty())
                            <div class="avz-empty">
                                <i class="fa fa-check-circle"></i>
                                Every item has healthy stock.
                            </div>
                        @else
                            <table class="table table-hover">
                                <tbody>
                                @foreach($lowStock as $p)
                                    <tr>
                                        <td>
                                            <strong>{{ $p->product_name }}</strong>
                                            <div class="avz-meter">
                                                <div class="avz-meter__fill {{ $p->stock == 0 ? 'avz-meter__fill--none' : 'avz-meter__fill--low' }}"
                                                     style="width: {{ min(100, $p->stock * 20) }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <strong>{{ $p->stock }}</strong><br>
                                            <small class="text-muted">left</small>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ url('/avenza/admin/products/'.$p->product_id.'/edit') }}"
                                               class="btn btn-white btn-xs">Restock</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'Karla', sans-serif";
    Chart.defaults.color = '#7A7168';

    var ink   = '#241C17',
        pine  = '#2F4739',
        sage  = '#6B8F73',
        brass = '#B8873F',
        rust  = '#A5453B',
        line  = '#DCD6CC';

    var peso = function (v) {
        return '\u20B1' + Number(v).toLocaleString('en-PH', { maximumFractionDigits: 0 });
    };

    // ---- Revenue, last 14 days -------------------------------------------
    var elSales = document.getElementById('chartSales');
    if (elSales) {
        var ctx  = elSales.getContext('2d');
        var fill = ctx.createLinearGradient(0, 0, 0, 240);
        fill.addColorStop(0, 'rgba(47, 71, 57, 0.22)');
        fill.addColorStop(1, 'rgba(47, 71, 57, 0)');

        new Chart(elSales, {
            type: 'line',
            data: {
                labels: @json($salesLabels),
                datasets: [{
                    label: 'Revenue',
                    data: @json($salesValues),
                    borderColor: pine,
                    backgroundColor: fill,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#FBFAF8',
                    pointBorderColor: pine,
                    pointBorderWidth: 2,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: ink,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function (c) { return peso(c.parsed.y); }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: line },
                        ticks: { callback: function (v) { return peso(v); } }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ---- Order mix --------------------------------------------------------
    var elStatus = document.getElementById('chartStatus');
    if (elStatus) {
        new Chart(elStatus, {
            type: 'doughnut',
            data: {
                labels: @json(array_map('ucfirst', $statusOrder)),
                datasets: [{
                    data: @json($statusValues),
                    backgroundColor: [brass, pine, sage, rust],
                    borderColor: '#FBFAF8',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: ink, padding: 10 }
                }
            }
        });
    }

    // ---- Units by room ----------------------------------------------------
    var elRooms = document.getElementById('chartRooms');
    if (elRooms) {
        new Chart(elRooms, {
            type: 'bar',
            data: {
                labels: @json($roomLabels),
                datasets: [{
                    label: 'Units',
                    data: @json($roomValues),
                    backgroundColor: walnutShades(@json(count($roomValues))),
                    borderRadius: 2,
                    barThickness: 26
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: ink, padding: 10, displayColors: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: line },
                        ticks: { precision: 0 }
                    },
                    y: {
                        border: { display: false },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Darkest bar first, so the ranking reads without needing the numbers
    function walnutShades(n) {
        var out = [];
        for (var i = 0; i < n; i++) {
            out.push('rgba(107, 74, 50, ' + (1 - (i * 0.62 / Math.max(n - 1, 1))).toFixed(2) + ')');
        }
        return out;
    }
})();
</script>
@endsection