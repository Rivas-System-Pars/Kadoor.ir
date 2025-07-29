@extends('front::user.layouts.master')

@section('user-content')
    <!-- Start Content -->
    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

        @if($histories->count())

            <div class="row">
                @if(session('message') == 'ok')

                    <div class="col-lg-12 text-center">
                        <div class="alert alert-success mt-4" role="alert">
                            <strong>افزایش موجودی با موفقیت انجام شد</strong>.
                        </div>
                    </div>

                @elseif(session('transaction-error'))
                    <div class="col-lg-12 text-center">
                        <div class="alert alert-danger mt-4" role="alert">
                            <strong>{{ session('transaction-error') }}</strong>.
                        </div>
                    </div>
                @endif

                <div class="col-12">
                    <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                        <h2>تاریخچه کیف پول</h2>
                        <a href="{{ route('front.wallet.create') }}" class="m-0 d-block">افزایش موجودی کیف پول</a>
                    </div>
                    <div class="dt-sl">
                        <div class="table-responsive">
                            <table class="table table-order">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>مبلغ (تومان)</th>
                                    <th>نوع تراکنش</th>
                                    <th>تاریخ</th>
                                    <th class="text-center">وضعیت</th>
                                    <th>جزییات</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($histories as $history)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            @php
                                                $is_deposit = $history->type == 'deposit';
                                            @endphp

                                            <td class="{{ $is_deposit ? 'text-success' : 'text-danger' }}">{{ number_format($history->amount) }}</td>
                                            <td>
                                                @if ($is_deposit)
                                                    افزایش اعتبار
                                                    <div class="badge badge-success ml-1">
                                                        <i class="mdi mdi-arrow-up"></i>
                                                    </div>
                                                @else
                                                    کاهش اعتبار
                                                    <div class="badge badge-danger ml-1">
                                                        <i class="mdi mdi-arrow-down"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="ltr">{{ jdate($history->created_at) }}</td>

                                            <td class="text-center">
                                                @if($history->status == 'success')
                                                    <div class="badge badge-pill badge-success badge-md">موفق</div>
                                                @else
                                                    <div class="badge badge-pill badge-danger badge-md">ناموفق</div>
                                                @endif
                                            </td>

                                            <td class="details-link">
                                                <a class="show-history" data-action="{{ route('front.wallet.show', ['wallet' => $history]) }}" href="#" onclick="return false;">
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
                    <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                        <h2>تاریخچه کیف پول</h2>
                        <a href="{{ route('front.wallet.create') }}" class="m-0 d-block">افزایش موجودی کیف پول</a>
                    </div>
                </div>
                <div class="col-12">
                    <div class="page dt-sl dt-sn pt-3">
                        <p>چیزی برای نمایش وجود ندارد</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-3">
            {{ $histories->links('front::components.paginate') }}
        </div>

    </div>
    <!-- End Content -->
@endsection

@push('scripts')
    <!-- show Modal -->
    <div class="modal fade" id="history-show-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel21">جزئیات تراکنش</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="history-detail" class="modal-body">


                </div>
            </div>
        </div>
    </div>

    <script src="{{ theme_asset('js/pages/wallet/index.js') }}"></script>
@endpush
