<div id="checkout-sidebar" class="col-xl-3 col-lg-4 col-12 w-res-sidebar sticky-sidebar">
    <div class="dt-sn mb-2 details">
        <ul class="checkout-summary-summary">
            <li>
                <span>مبلغ کل </span><span>{{ number_format($cart->price) }} {{ option('default_currency_id',"ریال") }}</span>
            </li>

            @if($cart->totalDiscount())
                <li class="checkout-summary-discount">
                    <span>تخفیف</span><span> {{ number_format($cart->totalDiscount()) }} تومان</span>
                </li>
            @endif

            @if (isset($carrier_id) && $cart->hasPhysicalProduct())
                <li>
                    <span>هزینه ارسال</span>
                    <span>
                        {{ $cart->shippingCost($city_id, $carrier_id) }}
                    </span>
                </li>
            @endif

        </ul>
        <div class="checkout-summary-devider">
            <div></div>
        </div>
        <div class="checkout-summary-content">
            <div class="checkout-summary-price-title">مبلغ قابل پرداخت:</div>
            <div class="checkout-summary-price-value">
                <span id="final-price" data-value="{{ $cart->finalPrice($city_id, $carrier_id ?? '') }}" class="checkout-summary-price-value-amount">{{ number_format($cart->finalPrice($city_id, $carrier_id ?? '')) }}</span>
                {{ option('default_currency_id',"ریال") }}
            </div>

            <button data-action="{{ route('front.cart') }}" data-redirect="{{ route('front.checkout') }}" id="checkout-link" type="button" class="btn-primary-cm btn-with-icon w-100 text-center pr-0 checkout_link">
                <i class="mdi mdi-arrow-left"></i>
                ادامه ثبت سفارش
            </button>

            <div>
                <span>
                    کالاهای موجود در سبد شما ثبت و رزرو نشده‌اند، برای ثبت سفارش
                    مراحل بعدی را تکمیل کنید.
                </span>
                <span class="help-sn" data-toggle="tooltip" data-html="true" data-placement="bottom"  title="<div class='help-container is-right'><div class='help-arrow'></div><p class='help-text'>محصولات موجود در سبد خرید شما تنها در صورت ثبت و پرداخت سفارش برای شما رزرو می‌شوند. در صورت عدم ثبت سفارش، فروشگاه ما هیچگونه مسئولیتی در قبال تغییر قیمت یا موجودی این کالاها ندارد.</p></div>">
                    <span class="mdi mdi-information-outline"></span>
                </span>
            </div>
        </div>
    </div>

</div>
