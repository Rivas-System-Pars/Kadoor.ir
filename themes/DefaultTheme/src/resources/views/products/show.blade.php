@extends('front::layouts.master', ['title' => $product->meta_title ?: $product->title])

@push('meta')
    @include('front::products.partials.product-meta')
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ theme_asset('css/vendor/fancybox.min.css') }}">
@endpush

@section('content')

    <!-- Start main-content -->
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">
            <!-- Start title - breadcrumb -->
            <div class="title-breadcrumb-special dt-sl mb-3">
                <div class="breadcrumb dt-sl">
                    <nav>
                        <a href="{{ route('front.index') }}">خانه</a>
                        <a href="{{ route('front.products.index') }}">محصولات</a>
                        @if ($product->category)

                            @foreach ($product->category->parents() as $parent)
                                <a href="{{ route('front.products.category', ['category' => $parent]) }}">{{ $parent->title }}</a>
                            @endforeach

                            <a href="{{ route('front.products.category', ['category' => $product->category]) }}">{{ $product->category->title }}</a>

                        @endif
                        <span>{{ $product->title }}</span>
                    </nav>
                </div>
            </div>
            <!-- End title - breadcrumb -->

            <!-- Start Product -->
            <div class="dt-sn mb-5 dt-sl">
                <div class="row">
                    <!-- Product Gallery-->
                    <div class="col-lg-4 col-md-6 pb-5 ps-relative">

                        @if(!$product->addableToCart())
                            <div class="product-timeout position-relative pt-5 mb-3">
                                <div class="promotion-badge not-available">
                                    ناموجود
                                </div>
                            </div>
                        @elseif($product->special)
                            <div class="product-timeout position-relative pt-5 mb-3">
                                <div class="promotion-badge">
                                    فروش ویژه
                                </div>
                            </div>
                        @endif

                        <ul class="gallery-options">
                            @if (auth()->check())
                                @php
                                    $favorite_product = auth()->user()->favorites()->where('product_id', $product->id)->first();
                                @endphp
                                <li>
                                    <button id="add-to-favorites" data-action="{{ route('front.favorites.store') }}" data-product="{{ $product->id }}" class="add-favorites {{ $favorite_product ? 'favorites' : '' }}"><i class="mdi mdi-heart"></i></button>
                                    @if ($favorite_product)
                                        <span class="tooltip-option">حذف از علاقمندی ها</span>
                                    @else
                                        <span class="tooltip-option">افزودن به علاقمندی ها</span>
                                    @endif
                                </li>
                            @endif

                            @if ($similar_products_count)
                                <a href="{{ route('front.products.compare', ['product1' => $product->id]) }}">
                                    <li>
                                        <button class="add-favorites"><i class="mdi mdi-compare"></i></button>
                                        <span class="tooltip-option">مقایسه</span>
                                    </li>
                                </a>
                            @endif

                            @if ($show_prices_chart)
                                <li>
                                    <button class="add-favorites" data-toggle="modal" data-target="#price-changes-modal"><i class="mdi mdi-chart-line"></i></button>
                                    <span class="tooltip-option">نمودار قیمت</span>
                                </li>
                            @endif

                        </ul>

                        @if($product->gallery()->count())
                            <div class="product-gallery">
                                <div class="product-carousel owl-carousel">
                                    @foreach ($product->gallery()->orderBy('ordering')->get() as $item)
                                        <div class="item">
                                            <a class="gallery-item" href="{{ asset($item->image) }}" data-fancybox="gallery" data-owl="one{{ $loop->index }}">
                                                <img data-src="{{ asset($item->image) }}" alt="{{ $product->title }}">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                                <ul class="product-thumbnails">
                                    @foreach ($product->gallery()->orderBy('ordering')->get() as $item)
                                        <li class="{{ ($loop->index == 0) ? 'active' : '' }}">
                                            <a href="#one{{ $loop->index }}" class="owl-thumbnail" data-slide="{{ $loop->index }}">
                                                <img data-src="{{ asset($item->image) }}" alt="{{ $product->title }}">
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>

                    @include('front::products.partials.product-info')
                </div>
            </div>
            <div class="dt-sn mb-5 px-0 dt-sl pt-0">
                <!-- Start tabs -->
                <section class="tabs-product-info mb-3 dt-sl">
                    <div class="ah-tab-wrapper dt-sl">
                        <div class="ah-tab dt-sl">
                            @if ($product->isDownload())
                                <a class="ah-tab-item" href="javascript:void(0)"><i class="mdi mdi-download"></i>فایل های محصول</a>
                            @endif

                            @if ($product->description)
                                <a class="ah-tab-item" href="javascript:void(0)"><i class="mdi mdi-glasses"></i>مشخصات کلی</a>
                            @endif

                            @if ($product->specificationGroups()->count())
                                <a class="ah-tab-item" href="javascript:void(0)"><i class="mdi mdi-format-list-checks"></i>مشخصات فنی</a>
                            @endif

                            <a class="ah-tab-item" href="javascript:void(0)"><i class="mdi mdi-comment-question-outline"></i>دیدگاه و پرسش و پاسخ</a>
                        </div>
                    </div>
                    <div class="ah-tab-content-wrapper product-info px-4 dt-sl">
                        @if ($product->isDownload())
                            @include('front::products.partials.links-tab')
                        @endif

                        @if ($product->description)
                            <div class="ah-tab-content dt-sl">
                                <div class="section-title text-sm-title title-wide no-after-title-wide mb-0 dt-sl">
                                    <h2>مشخصات کلی</h2>
                                </div>

                                <div class="description-product dt-sl mt-3 mb-3">
                                    <div class="container">
                                        <p>{!! $product->description !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($product->specificationGroups()->count())
                            <div class="ah-tab-content params dt-sl">
                                <div class="section-title text-sm-title title-wide no-after-title-wide mb-0 dt-sl">
                                    <h2>مشخصات فنی</h2>
                                </div>
                                @foreach($product->specificationGroups->unique() as $group)
                                    <section>
                                        <h3 class="params-title">{{ $group->name }}</h3>
                                        <ul class="params-list">
                                            @foreach($product->specifications()->where('specification_group_id', $group->id)->get() as $specification)
                                                <li>
                                                    <div class="params-list-key">
                                                        <span class="d-block">{{ $specification->name }}</span>
                                                    </div>
                                                    <div class="params-list-value">
                                                            <span class="d-block">
                                                                {!! nl2br(htmlentities($specification->pivot->value)) !!}
                                                            </span>
                                                    </div>

                                                </li>
                                            @endforeach
                                        </ul>
                                    </section>
                                @endforeach
                            </div>
                        @endif

                        <div class="ah-tab-content dt-sl">
                            @include('front::components.comments', ['model' => $product, 'route_link' => route('front.product.comments', ['product' => $product]), 'message' => 'هیچ دیدگاهی برای این محصول ثبت نشده است.' ])

                        </div>
                    </div>
                </section>
                <!-- End tabs -->
            </div>
            <!-- End Product -->

            @if($related_products->count())
                <!-- Start Product-Slider -->
                <section class="slider-section dt-sl mb-5">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="section-title text-sm-title title-wide no-after-title-wide">
                                <h2>محصولات مرتبط</h2>
                            </div>
                        </div>

                        <!-- Start Product-Slider -->
                        <div class="col-12">
                            <div class="product-carousel carousel-lg owl-carousel owl-theme">
                                @foreach ($related_products as $related_product)
                                    @include('front::partials.product-block', ['product' => $related_product])
                                @endforeach
                            </div>

                        </div>
                        <!-- End Product-Slider -->

                    </div>
                </section>
                <!-- End Product-Slider -->
            @endif

        </div>
    </main>
    <!-- End main-content -->


    @if(!$product->addableToCart())
        <!-- Start Modal stocknotify -->
        <div class="modal fade" id="modal-stock-notify" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-md send-info modal-dialog-centered"
                role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">
                            <i class="now-ui-icons location_pin"></i>
                            اطلاع از موجودی
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-ui dt-sl">
                                    <form class="form-account" action="#">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-12 mb-2">
                                                <div class="form-row-title">
                                                    <h4>
                                                        نام و نام خانوادگی
                                                    </h4>
                                                </div>
                                                <div class="form-row">
                                                    <input class="input-ui pr-2 text-right"
                                                        type="text"
                                                        name="name"
                                                        id="stock-name"
                                                        placeholder="نام خود را وارد نمایید" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 mb-2">
                                                <div class="form-row-title">
                                                    <h4>
                                                        شماره موبایل
                                                    </h4>
                                                </div>
                                                <div class="form-row">
                                                    <input
                                                        class="input-ui pl-2 dir-ltr text-left"
                                                        type="text"
                                                        name="mobile"
                                                        id="stock-mobile"
                                                        placeholder="09xxxxxxxxx" required>
                                                </div>
                                            </div>

                                            <div class="col-12 pr-4 pl-4 text-center">
                                                <button id="sendStockNotifyBtn" type="button" class="btn btn-md btn-primary btn-submit-form" data-dismiss="modal">به من اطلاع بده</button>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal stocknotify -->
    @endif


    @if ($show_prices_chart)
        <!-- Modal -->
        <div class="modal fade" id="price-changes-modal" tabindex="-1" aria-labelledby="price-changes-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header pb-0">
                        <h5 class="modal-title" id="price-changes-modal-label">نمودار قیمت فروش</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body chart-area">
                        <strong class="text-muted">{{ $product->title }}</strong>
                        <p class="mt-1 text-muted" id="selected-chart-price-title"></p>
                        <div>
                            <div id="empty-chart" class="empty-chart" style="display: none">
                                <p>در سی روز گذشته تغییر قیمتی برای این کالا ثبت نشده است.</p>
                            </div>
                            <div id="chart" class="ltr"></div>
                        </div>
                        <ul class="chart-prices-label">
                            @foreach ($product->prices()->orderBy('stock', 'desc')->get() as $chart_price)
                                <li>
                                    <label data-action="{{ route('front.products.priceChart', ['price' => $chart_price]) }}" data-title="{{ $chart_price->getAttributesName() }}" title="{{ $chart_price->getAttributesName() }}">
                                        <span>{{ $chart_price->getAttributesName() }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ theme_asset('js/vendor/jquery.fancybox.min.js') }}"></script>
    <script src="{{ theme_asset('js/plugins/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ theme_asset('js/pages/products/show.js') }}?v=9"></script>
    <script src="{{ theme_asset('js/pages/comments.js') }}"></script>
@endpush
