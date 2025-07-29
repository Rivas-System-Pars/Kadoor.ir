@extends('back.layouts.printable')

@push('styles')
    <link rel="stylesheet" href="{{ asset('back/assets/css/pages/orders/print.css') }}?v=3">
    <style>
        @media print {
            @page {
                size: landscape
            }
        }

        .p-border-right {
            border-right: 2px solid #6e7275 !important;
        }

        .p-border-left {
            border-left: 2px solid #6e7275 !important;
        }

        .p-border-top {
            border-top: 2px solid #6e7275 !important;
        }

        .p-border-bottom {
            border-bottom: 2px solid #6e7275 !important;
        }

        .p-border {
            border: 2px solid #6e7275 !important;
        }

    </style>
@endpush

@section('content')
    <div class="container pt-1">
        <div class="row p-border" dir="rtl">
            <div class="col-6 px-0">
                <div class="pt-3 p-1">
                    <ul class="list-unstyled line-height-2">
                        <li><b>گیرنده:</b> <span>{{ $order->province->name ?? '' }} - {{ $order->city->name ?? '' }} - {{ $order->address }}</span></li>
                        <li><b>نام کامل:</b> <span>{{ $order->name }}</span></li>
                        <li><b>کدپستی</b> <span>{{ $order->postal_code }}</span></li>
                        <li><b>تلفن:</b> <span>{{ $order->mobile }}</span></li>
                        <li><b>تاریخ سفارش:</b> <span>{{ jdate($order->created_at)->format('Y/m/d') }}</span></li>
                    </ul>
                    <div>
                        <p class="text-center">
                            <img class="w-100" style="height: 40px;object-fit: contain" src="{{ barcode($order->id) }}" alt="barcode">
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-6 px-0">
                <div class=" p-border-right p-1">
                    <div>
                        <h4 class="text-center p-border-bottom p-2">
                            @if (option('factor_logo'))
                                <img src="{{ asset(option('factor_logo')) }}" alt="factor_logo"  style="max-height: 50px;">
                            @endif
                            {{ option('info_site_title') }}
                        </h4>
                    </div>
                    <ul class="list-unstyled line-height-2">
                        <li><b>آدرس:</b> <span>{{ option('info_address') }}</span></li>
                        <li><b>کدپستی:</b> <span>{{ option('info_postal_code') }}</span></li>
                        <li><b>تلفن:</b> <span>{{ option('info_tel') }}</span></li>
                        <li><b>ایمیل:</b> <span>{{ option('info_email') }}</span></li>
                        <li><b>وب سایت:</b> <span>{{ url('/') }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="print-footer col-12 px-0">
                <ul class="d-flex p-border-top m-0 p-1 justify-content-around list-unstyled">
                    <li class="px-1"><b>شناسه سفارش:</b> <span>{{ $order->id }}</span></li>
                    <li class="p-border-left"></li>
                    <li class="px-1"><b>روش حمل و نقل:</b> <span>{{ $order->carrier->title }}</span></li>
                    <li class="p-border-left"></li>
                    <li class="px-1"><b>روش پرداخت:</b> <span>{{ $order->carrier->carrige_forward ? 'پس کرایه' : 'پرداخت آنلاین' }}</span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container p-0 mt-1 d-print-none">
        <div class="row">
            <div class="col-12 text-right">
                <button onclick="window.print();" class="btn btn-light">چاپ</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        setTimeout(function () { window.print(); }, 500);
    </script>
@endpush
