@extends('front::user.layouts.master')

@section('user-content')
    <!-- Start Content -->
    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
        <div class="row">
            <div class="col-lg-6">
                <div class="px-3">
                    <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2">
                        <h2>اطلاعات شخصی</h2>
                    </div>
                    <div class="profile-section dt-sl">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="label-info">
                                    <span>نام:</span>
                                </div>
                                <div class="value-info">
                                    <span>{{ $user->first_name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="label-info">
                                    <span>نام خانوادگی:</span>
                                </div>
                                <div class="value-info">
                                    <span>{{ $user->last_name }}</span>
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="label-info">
                                    <span>شماره تلفن همراه:</span>
                                </div>
                                <div class="value-info">
                                    <span>{{ $user->username }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="label-info">
                                    <span>ایمیل:</span>
                                </div>
                                <div class="value-info">
                                    <span>{{ $user->email ?: '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="label-info">
                                    <span>استان:</span>
                                </div>
                                <div class="value-info">
                                    <span>{{ $user->address ? $user->address->province->name : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="label-info">
                                    <span>شهر:</span>
                                </div>
                                <div class="value-info">
                                    <span>{{ $user->address ? $user->address->city->name : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="label-info">
                                    <span>کد پستی:</span>
                                </div>
                                <div class="value-info">
                                    <span>{{ $user->address ? $user->address->postal_code : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="label-info">
                                    <span>آدرس کامل:</span>
                                </div>
                                <div class="value-info address">
                                    <span title="{{ $user->address ? $user->address->address : '-' }}">{{ $user->address ? ellips_text($user->address->address, 80) : '-' }}</span>
                                </div>
                            </div>

                        </div>
                        <div class="profile-section-link">
                            <a href="{{ route('front.user.profile.edit') }}" class="border-bottom-dt">
                                <i class="mdi mdi-account-edit-outline"></i>
                                ویرایش اطلاعات شخصی
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="px-3">
                    <div
                        class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2">
                        <h2>لیست آخرین علاقه‌مندی‌ها</h2>
                    </div>
                    <div class="profile-section dt-sl">
                        @if ($user->favorites()->count())
                            <ul class="list-favorites">
                                @foreach ($user->favorites()->latest()->take(3)->get() as $favorite)
                                    <li>
                                        <a href="{{ route('front.products.show', ['product' => $favorite->product]) }}">
                                            <img data-src="{{  $favorite->product->image ? asset($favorite->product->image) : asset('/no-image-product.png') }}" alt="">
                                            <span>{{ $favorite->product->title }}</span>
                                        </a>
                                        <button data-action="{{ route('front.favorites.destroy', ['favorite' => $favorite]) }}" data-toggle="modal" data-target="#favorite-delete-modal" class="favorite-remove-btn">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="profile-section-link">
                                <a href="{{ route('front.favorites.index') }}" class="border-bottom-dt">
                                    <i class="mdi mdi-square-edit-outline"></i>
                                    مشاهده و ویرایش لیست مورد علاقه
                                </a>
                            </div>
                        @else
                            <p class="mt-2">چیزی برای نمایش وجود ندارد!</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($last_orders->count())
                <div class="col-lg-12 mt-3">
                    <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                        <h2>آخرین سفارش‌ها</h2>
                    </div>
                    <div class="dt-sl">
                        <div class="table-responsive">
                            <table class="table table-order">
                                <thead>
                                <tr>
                                    <td>#</td>
                                    <th>شماره سفارش</th>
                                    <th>تاریخ ثبت سفارش</th>
                                    <th>وضعیت پرداخت</th>
                                    <th>جزییات</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($last_orders as $order)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td class="text-info">{{ $order->id }}</td>
                                            <td>{{ jdate($order->created_at)->format('%d %B %Y') }}</td>
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
                                <tr>
                                    <td class="link-to-orders" colspan="7"><a href="{{ route('front.orders.index') }}">مشاهده لیست سفارش ها</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

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
