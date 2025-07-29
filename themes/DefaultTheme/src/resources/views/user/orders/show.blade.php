@extends('front::user.layouts.master')

@section('user-content')
    <!-- Start Content -->
    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
        <div class="row">

            @if(session('message') == 'ok')

                <div class="col-12">
                    <div class="checkout-alert dt-sn mb-4">
                        <div class="circle-box-icon successful">
                            <i class="mdi mdi-check-bold"></i>
                        </div>
                        <div class="checkout-alert-title">
                            <h4> سفارش شما با شماره سفارش<span class="checkout-alert-highlighted checkout-alert-highlighted-success">{{ $order->id }}</span>
                                با موفقیت در سیستم ثبت شد.
                            </h4>
                        </div>

                    </div>
                </div>
            @elseif(session('transaction-error'))
                <div class="col-12">
                    <div class="checkout-alert dt-sn mb-4">
                        <div class="circle-box-icon failed">
                            <i class="mdi mdi-close"></i>
                        </div>
                        <div class="checkout-alert-title">
                            <h4> سفارش <span class="checkout-alert-highlighted checkout-alert-highlighted-success">{{ $order->id }}</span>
                                ثبت شد اما پرداخت ناموفق بود.
                            </h4>
                        </div>
                        <div class="checkout-alert-content">
                            <p>
                                <span class="checkout-alert-content-failed">برای جلوگیری از لغو سیستمی سفارش، تا ۱
                                    ساعت پس از ثبت سفارش پرداخت را انجام دهید.</span>
                                <br>
                                <span class="checkout-alert-content-small px-res-1">
                                    چنانچه طی این فرایند مبلغی از حساب شما کسر شده است، طی ۷۲ ساعت آینده به حساب شما
                                    باز خواهد گشت.
                                </span>
                            </p>
                        </div>
                        @if($order->status == 'unpaid')
                            <div class="text-center">
                                <a href="{{ route('front.orders.pay', ['order' => $order]) . '?gateway=' . ($order->gatewayRelation ? $order->gatewayRelation->key : 'wallet') }}" class="btn btn-light">
                                    پرداخت سفارش
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-12 text-center">
                    <div class="alert alert-danger mt-4" role="alert">
                        <strong>{{ session('transaction-error') }}</strong>.
                    </div>
                </div>
            @elseif(session('error'))

                <div class="col-lg-12 text-center">
                    <div class="alert alert-danger mt-4" role="alert">
                        <strong>{{ session('error') }}</strong>.
                    </div>
                </div>
            @endif

            <div class="col-12">
                <div class="profile-navbar">
                    <h4>سفارش شماره <span class="font-en">{{ $order->id }}</span><span>ثبت شده در تاریخ {{ jdate($order->created_at)->format('%d %B %Y') }}</span></h4>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="dt-sl dt-sn">
                    <div class="row table-draught px-3">
                        <div class="col-md-6 col-sm-12">
                            <span class="title">تحویل گیرنده:</span>
                            <span class="value">{{ $order->name }}</span>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <span class="title">شماره تماس تحویل گیرنده:</span>
                            <span class="value">{{ $order->mobile }}</span>
                        </div>

                        @if ($order->hasPhysicalProduct())
                            <div class="col-md-6 col-sm-12">
                                <span class="title">هزینه ارسال:</span>
                                @if ($order->shipping_cost)
                                    <span class="value">{{ number_format($order->shipping_cost)." ".option('default_currency_id',"ریال") }}</span>
                                @elseif ($order->carrier && $order->carrier->carrige_forward)
                                    <span class="value">پس کرایه</span>
                                @else
                                    <span class="value">رایگان</span>
                                @endif

                            </div>

                        
                            <div class="col-md-6 col-sm-12">
                                <span class="title">نحوه ارسال سفارش:</span>
                                <span class="value">{{ $order->carrier ? $order->carrier->title : 'نامشخص' }}</span>
                            </div>
                        @endif

                        @if ($order->province)
                            <div class="col-md-6 col-sm-12">
                                <span class="title">آدرس:</span>
                                <span class="value">{{ $order->province->name . ' - ' . $order->city->name }}</span>
                            </div>
                        @endif


                        @if ($order->address)
                            <div class="col-md-6 col-sm-12">
                                <span class="title">آدرس کامل:</span>
                                <span class="value">{{ $order->address }}</span>
                            </div>
                        @endif

                        @if ($order->postal_code)
                            <div class="col-md-6 col-sm-12">
                                <span class="title">کد ملی:</span>
                                <span class="value">{{ $order->national_code }}</span>
                            </div>
                        @endif
						@if ($order->telephone)
                            <div class="col-md-6 col-sm-12">
                                <span class="title">شماره ثابت:</span>
                                <span class="value">{{ $order->telephone }}</span>
                            </div>
                        @endif
						@if ($order->national_code)
                            <div class="col-md-6 col-sm-12">
                                <span class="title">کد پستی:</span>
                                <span class="value">{{ $order->national_code }}</span>
                            </div>
                        @endif
                        <div class="col-md-6 col-sm-12">
                            <span class="title">توضیحات سفارش:</span>
                            <span class="value">{{ ($order->walletHistory ? "پرداخت با کیف پول" : "پرداخت آنلاین")." ".optional($order)->description }}</span>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <span class="title">وضعیت پرداخت:</span>
                            <span class="value">
                                @if($order->status == 'paid')
                                    پرداخت شده
                                @elseif($order->status == 'unpaid')
                                    پرداخت نشده ( انتخاب درگاه پرداخت )
                                @else
                                    لغو شده
                                @endif
                            </span>

                            @if($order->status == 'unpaid')
                                <form action="{{ route('front.orders.pay', ['order' => $order]) }}" method="GET">
                                    <div class="row p-0">
                                        <div class="col-sm-8 border-none py-0 mb-2 mb-sm-0">
                                            <select class="form-control py-0" name="gateway" required>
                                                <option value="">انتخاب کنید</option>

                                                @if ($wallet->balance >= $order->price)
                                                    <option value="wallet">پرداخت با کیف پول</option>
                                                @elseif ($wallet->balance)
                                                    <option value="wallet">شارژ و پرداخت با کیف پول</option>
                                                @endif

                                                @foreach ($gateways as $gateway)
                                                    <option value="{{ $gateway->key }}">{{ $gateway->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4 border-none py-0">
                                            <button type="submit" class="btn btn-light">
                                                پرداخت سفارش
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <span class="title">جمع تخفیف:</span>
                            <span class="value">{{ number_format($order->totalDiscount())." ".option('default_currency_id',"ریال") }}</span>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <span class="title">جمع قیمت:</span>
                            <span class="value">{{ number_format($order->price)." ".option('default_currency_id',"ریال") }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->status == 'paid' && $order->hasPhysicalProduct())
                <div class="col-12 mb-4">
                    <section class="slider-section dt-sl mb-5">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="section-title text-sm-title title-wide no-after-title-wide">
                                    <h2>وضعیت ارسال @if($order->shipping_status == 'canceled') <small class="text-danger">( ارسال لغو شده است )</small> @endif </h2>
                                </div>
                            </div>

                            <!-- Start Profile-order-step-Slider -->
                            <div class="col-12">
                                <div class="profile-order-steps  owl-carousel owl-theme">
                                    <div class="item profile-order-steps-item {{ in_array($order->shipping_status, ['pending', 'wating', 'sent']) ? 'is-active' : '' }}">
                                        <img data-src="{{ theme_asset('img/svg/0eab59b0.svg') }}">
                                        <span>در حال بررسی</span>
                                    </div>

                                    <div class="item profile-order-steps-item {{ in_array($order->shipping_status, ['wating', 'sent']) ? 'is-active' : '' }}">
                                        <img data-src="{{ theme_asset('img/svg/3db3cdeb.svg') }}">
                                        <span>منتظر ارسال</span>
                                    </div>
                                    <div class="item profile-order-steps-item last-item {{ in_array($order->shipping_status, ['sent']) ? 'is-active' : '' }}">
                                        <img data-src="{{ theme_asset('img/svg/332b9ff1.svg') }}">
                                        <span>ارسال شد</span>
                                    </div>

                                </div>
                            </div>
                            <!-- End Profile-order-step-Slider -->

                        </div>
                    </section>
                </div>
            @endif

            <div class="col-12">
                <div
                        class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                    <h2>همه سفارش‌ها</h2>
                </div>
                <div class="dt-sl">
                    <div class="table-responsive">
                        <table class="table table-order table-order-details">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام محصول</th>
                                <th>تعداد</th>
                                <th>قیمت واحد</th>
                                <th>قیمت کل</th>
                                <th>تخفیف</th>
                                <th>قیمت نهایی</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="details-product-area">
                                                @if($item->product)
                                                    <a href="{{ route('front.products.show', ['product' => $item->product]) }}"
                                                        target="_blank"><img class="thumbnail-product"
                                                            src="{{ $item->product->image ? $item->product->image : '/empty.jpg' }}"></a>
                                                @else
                                                    <img class="thumbnail-product" src="/empty.jpg">
                                                @endif

                                                <h5 class="details-product">
                                                    <span>{{ $item->title }}</span>

                                                    @if ($item->get_price)
                                                        @foreach ($item->get_price->get_attributes as $attribute)

                                                            @if ($attribute->group->type == 'color')

                                                                <span class="order-product-color" style="background-color: {{ $attribute->value }};"></span>
                                                                <span>{{ $attribute->group->name }}: {{ $attribute->name }}</span>
                                                            @else
                                                                <span>{{ $attribute->group->name }}: {{ $attribute->name }}</span>
                                                            @endif

                                                        @endforeach
                                                    @endif

                                                </h5>
                                            </div>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->realPrice())." ".option('default_currency_id',"ریال") }}</td>
                                        <td>{{ number_format($item->quantity * $item->realPrice())." ".option('default_currency_id',"ریال") }}</td>
                                        <td>{{ $item->discount ? $item->discount . '%' : 0 }}</td>
                                        <td>{{ number_format($item->price * $item->quantity)." ".option('default_currency_id',"ریال") }}</td>
                                        <td>
                                            @if ($item->product && $item->product->isDownload() && $item->get_price && $item->get_price->isDownloadable())
                                                <a href="{{ $item->get_price->downloadLink() }}" class="btn btn-success mb-2">دانلود</a>
                                            @endif

                                            @if($item->product)
                                                <a href="{{ route('front.products.show', ['product' => $item->product]) }}" class="btn btn-info mb-2">مشاهده</a>

                                            @else
                                                <button class="btn btn-info mb-2" disabled>مشاهده</button>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($order->transactions->count())
                    <div class="section-title text-sm-title title-wide no-after-title-wide mb-0 dt-sl px-res-1 mt-3">
                        <h2>جزئیات پرداخت</h2>
                    </div>
                    <section class="checkout-details dt-sl dt-sn mb-4 pt-2 pb-3 pl-3 pr-3">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="checkout-orders-table">
                                        <tbody>
                                            <tr>
                                                <td class="numrow">
                                                    <p>
                                                        ردیف
                                                    </p>
                                                </td>

                                                <td class="id">
                                                    <p>
                                                        شماره تراکنش
                                                    </p>
                                                </td>
                                                <td class="date">
                                                    <p>
                                                        تاریخ
                                                    </p>
                                                </td>
                                                <td class="price">
                                                    <p>
                                                        مبلغ
                                                    </p>
                                                </td>
                                                <td class="status">
                                                    <p>
                                                        وضعیت
                                                    </p>
                                                </td>
                                            </tr>

                                            @foreach($order->transactions()->latest()->get() as $transaction)
                                                <tr>
                                                    <td class="numrow">
                                                        <p>{{ $loop->iteration }}</p>
                                                    </td>

                                                    <td class="id">
                                                        <p>{{ $transaction->id }}</p>
                                                    </td>
                                                    <td class=" date">
                                                        <p>{{ jdate($transaction->created_at)->format('%d %B %Y H:i:s') }}</p>
                                                    </td>
                                                    <td class="price">
                                                        <p> {{ number_format($transaction->amount)." ".option('default_currency_id',"ریال") }}</p>
                                                    </td>
                                                    <td class="status">
                                                        <p>
                                                            @if($transaction->status)
                                                                <span class="text-success">پرداخت موفق</span>
                                                            @else
                                                                <span class="text-danger">پرداخت ناموفق</span>
                                                            @endif
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>

                @endif
            </div>


        </div>
    </div>
    <!-- End Content -->
@endsection
