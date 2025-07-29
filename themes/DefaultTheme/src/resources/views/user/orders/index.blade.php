@extends('front::user.layouts.master')

@section('user-content')
    <!-- Start Content -->
    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

        @if($orders->count())

            <div class="row">
                <div class="col-12">
                    <div
                            class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                        <h2>همه سفارش‌ها</h2>
                    </div>
                    <div class="dt-sl">
                        <div class="table-responsive">
                            <table class="table table-order">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>شماره سفارش</th>
                                    <th>تاریخ ثبت سفارش</th>
                                    <th>مبلغ کل</th>
                                    <th>وضعیت پرداخت</th>
                                    <th>جزییات</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td class="text-info">{{ $order->id }}</td>
                                            <td>{{ jdate($order->created_at)->format('%d %B %Y') }}</td>
                                            <td>{{ number_format($order->price)." ".option('default_currency_id',"ریال") }}</td>
                                            <td>
                                                @if($order->status == 'paid')
                                                    <span class="text-success">پرداخت شده</span>
                                                @elseif($order->status == 'unpaid')
                                                    <span class="text-danger">پرداخت نشده</span>
                                                @else
                                                    <span class="text-danger">لغو شده</span>
                                                @endif
                                            </td>
                                            <td class="details-link">
                                                <a href="{{ route('front.orders.show', ['order' => $order]) }}">
                                                    <i class="mdi mdi-chevron-left"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="row">
                <div class="col-12">
                    <div class="page dt-sl dt-sn pt-3">
                        <p>چیزی برای نمایش وجود ندارد</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-3">
            {{ $orders->links('front::components.paginate') }}
        </div>

    </div>
    <!-- End Content -->
@endsection
