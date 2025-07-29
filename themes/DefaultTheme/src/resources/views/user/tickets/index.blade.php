@extends('front::user.layouts.master')

@section('user-content')
    <!-- Start Content -->
    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">

        <div class="row">
            <div class="col-md-12">
                <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                    <h2>لیست تیکت‌ ها</h2>
                    <a href="{{ route('front.tickets.create') }}" class="btn btn-info d-block">ثبت تیکت جدید</a>
                </div>
            </div>
        </div>

        @if($tickets->count())

            <div class="row">
                <div class="col-12">
                    <div class="dt-sl">
                        <div class="table-responsive">
                            <table class="table table-order">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>شماره تیکت</th>
                                    <th>موضوع</th>
                                    <th>تاریخ ثبت تیکت</th>
                                    <th>اولویت</th>
                                    <th>وضعیت</th>
                                    <th>جزییات</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td class="text-info">{{ $ticket->id }}</td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>{{ jdate($ticket->created_at)->format('%d %B %Y') }}</td>
                                            <td>{{ $ticket->priorityText() }}</td>
                                            <td>
                                                {{ $ticket->statusText() }}
                                            </td>
                                            <td class="details-link">
                                                <a href="{{ route('front.tickets.show', ['ticket' => $ticket]) }}">
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
            {{ $tickets->links('front::components.paginate') }}
        </div>

    </div>
    <!-- End Content -->
@endsection
