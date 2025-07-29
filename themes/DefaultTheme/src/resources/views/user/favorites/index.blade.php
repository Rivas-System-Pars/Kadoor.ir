@extends('front::user.layouts.master')

@section('user-content')
    <!-- Start Content -->
    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

        @if($favorites->count())

            <div class="row">
                <div class="col-12">
                    <div
                        class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                        <h2>علاقمندی ها</h2>
                    </div>
                    <div class="dt-sl">
                        <div class="row">
                            @foreach ($favorites as $favorite)
                                <div class="col-lg-6 col-md-12">
                                    <div class="card-horizontal-product">
                                        <div class="card-horizontal-product-thumb">
                                            <a href="{{ route('front.products.show', ['product' => $favorite->product]) }}">
                                                <img data-src="{{  $favorite->product->image ? asset($favorite->product->image) : asset('/no-image-product.png') }}" alt="{{ $favorite->product->title }}">
                                            </a>
                                        </div>
                                        <div class="card-horizontal-product-content">
                                            <div class="card-horizontal-product-title">
                                                <a href="{{ route('front.products.show', ['product' => $favorite->product]) }}">
                                                    <h3>{{ $favorite->product->title }}</h3>
                                                    @if ($favorite->product->addableToCart())
                                                        <span class="label-card-horizontal-product text-success">موجود</span>
                                                    @else
                                                        <span class="label-card-horizontal-product">ناموجود</span>
                                                    @endif
                                                </a>
                                            </div>

                                            <div class="card-horizontal-product-buttons">
                                                <a href="{{ route('front.products.show', ['product' => $favorite->product]) }}" class="btn">مشاهده محصول</a>
                                                <button data-action="{{ route('front.favorites.destroy', ['favorite' => $favorite]) }}" data-toggle="modal" data-target="#favorite-delete-modal" class="remove-btn favorite-remove-btn">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
            {{ $favorites->links('front::components.paginate') }}
        </div>

    </div>
    <!-- End Content -->


@endsection

@push('scripts')
    <!-- Start favorite delete -->
    <div class="modal fade" id="favorite-delete-modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="now-ui-icons location_pin"></i>
                        حذف از لیست علاقمندی ها
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <p>آیا تمایل به حذف این محصول از لیست علاقمندی ها دارید؟</p>

                            <div class="form-ui dt-sl">
                                <form id="favorite-remove-form" action="#" method="POST">
                                    <div class="modal-body text-center">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-danger btn-md">بله حذف شود</button>
                                        <button class="btn btn-light" data-dismiss="modal">لغو</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End favorite delete -->

    <script src="{{ theme_asset('js/pages/favorites/index.js') }}"></script>
@endpush
