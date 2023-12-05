@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Purchasing</a></li>
    <li class="breadcrumb-item"><a href="{{route('purchase.index')}}">Purchase Order</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $purchase->code }}</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="card-title">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="card-title">Details Orders {{ $purchase->code }}</h6>
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                </div>
            </div>
        </div>
        <div class="card-body-wrap">
            <div class="detail-contact-wrap">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="mb-3">{{$purchase->contactname}}</h3>
                        <div class="address mb-3 d-flex">
                            <label for="" class="me-3">Expected Arrival</label>
                            <p class="text-muted">{{ $purchase->expected_arrival }}</p>
                        </div>
                        <div class="address mb-3 d-flex">
                            <label for="" class="me-3">Deliver To</label>
                            <p class="text-muted">{{ $purchase->warehousename }}</p>
                        </div>
                        <div class="address mb-3 d-flex">
                            <label for="" class="me-3">Order Status</label>
                            <p class="text-muted">{{ $purchase->status }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2" style="text-align:right">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{ route('vendor-bills.create', ['purchase_id' => $purchase->id]) }}" class="btn btn-primary w-100" disable>Vendor Bills</a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('purchase.receive', ['id' => $purchase->id]) }}" class="btn btn-primary mb-2 w-100">Receipt Product</a>
                            </div>
                            <div class="col-md-4">
                                <form method="POST" action="{{ route('purchase.sendToSlack', ['purchase' => $purchase->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">Kirim ke Slack</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered" id="productTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Tax</th>
                                    <th>Analytics</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $dataProduct = json_decode($purchase->data_product, true);
                                @endphp
                                @foreach($purchaseDetails as $details)
                                <tr>
                                    <td>{{$details['product_name']}}</td>
                                    <td>Rp. {{number_format($details['unit_price'], 0,',','.')}}</td>
                                    <td>{{$details['quantity']}}</td>
                                    <td>{{$details['tax']}}</td>
                                    <td>
                                        @php
                                            // Ambil nama produk berdasarkan product_id
                                            $AnalyticsName = \App\AnalyticsAccount::find($details['analytics'])->name;
                                        @endphp
                                        {{$AnalyticsName}}</td>
                                    <td>Rp. {{ number_format($details['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="title-total mt-3" style="text-align:right;">
                            <h4>Total : Rp.{{ number_format($purchase->total, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/js/select2.js') }}"></script>
<script src="{{ asset('assets/js/flatpickr.js') }}"></script>
<style>
    label.me-3 {
        width : 25%;
    }
    .detail-contact-wrap p.text-muted {
        width : 80%;
    }

    .accounting-wrap label.me-3 {
        width : 25%;
    }

    .btn-disabled {
        pointer-events: none; /* Mencegah tindakan pointer (klik, hover, dll.) */
        opacity: 0.5; /* Mengurangi kejelasan tampilan untuk menunjukkan status dinonaktifkan */
        text-decoration: none; /* Menghapus garis bawah tautan (opsional) */
        cursor: not-allowed; /* Menunjukkan kursor tidak diizinkan (opsional) */
    }

    @media (max-width : 678px){
        label.me-3 {
            width : 30%;
        }
        .detail-contact-wrap p.text-muted {
            width : 70%;
        }
        .col-md-6.align-self-center, .col-md-6.text-right.align-self-center {
            width : 50%;
        }
    }
</style>
@endpush