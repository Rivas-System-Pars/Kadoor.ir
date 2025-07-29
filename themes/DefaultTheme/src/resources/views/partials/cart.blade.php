@php
    $cart = isset($render_cart) && !empty($render_cart) ? $render_cart : $cart;
@endphp
@if($cart && $cart->products()->count())
    <li class="nav-item" id="cart-list-item">
        <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false">
            <span class="label-dropdown">سبد خرید</span>
            <i class="mdi mdi-cart-outline"></i>
            <span class="count">{{ $cart->products()->count() }}</span>
        </a>
        <div class="dropdown-menu cart dropdown-menu-sm dropdown-menu-left">
            <div class="dropdown-header">سبد خرید</div>
            <div class="dropdown-list-icons">
@foreach ($cart->products as $product)
                    <a href="{{ route('front.products.show', ['product' => $product]) }}" class="dropdown-item">
                        <div class="dropdown-item-icon">
                            <img src="{{ $product->image ? $product->image : '/empty.jpg' }}" alt="{{ $product->title }}">
                        </div>
                        <div class="mr-3 cart-title">
                            {{ $product->title }}

                                @php
                                    $cart_product_price = $product->prices()->find($product->pivot->price_id);
                                @endphp

                                <div class="pt-1">{{ number_format($cart_product_price->discountPrice() * $product->pivot->quantity) }} تومان</div>
                                @if($cart_product_price->discount)
                                    <del class="text-danger">{{ number_format($cart_product_price->tomanPrice() * $product->pivot->quantity) }} تومان</del>
                                @endif

                        </div>
                    </a>
                @endforeach
                
            </div>
            <hr>
            <div class="dropdown-footer text-center">
                <div class="dt-sl mb-3">
                    <span class="float-right">جمع :</span>
                    <span class="float-left">{{ number_format($cart->discountPrice()) }} تومان</span>
                </div>
                <a href="{{ route('front.cart') }}" class="btn btn-success">مشاهده سبد خرید</a>
                <a href="{{ route('front.checkout') }}" class="btn btn-primary">پرداخت</a>
            </div>
        </div>
    </li>

@else

    <li class="nav-item" id="cart-list-item">
        <a class="nav-link" href="#" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false">
            <span class="label-dropdown">سبد خرید</span>
            <i class="mdi mdi-cart-outline"></i>
        </a>
        <div class="dropdown-menu cart dropdown-menu-sm dropdown-menu-left text-center">
            <p class="pt-2">سبد خرید شما خالی است</p>
        </div>
    </li>

@endif

