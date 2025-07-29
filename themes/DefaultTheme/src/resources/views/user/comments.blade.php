@extends('front::user.layouts.master')

@section('user-content')
    <!-- Start Content -->
    <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
        <div class="row">
            <div class="col-12">
                <div class="section-title text-sm-title title-wide mb-1 no-after-title-wide dt-sl mb-2 px-res-1">
                    <h2>دیدگاه های شما</h2>
                </div>
                <div class="dt-sl">
                    <div class="row">
                        @if($comments->count())
                            @foreach($comments as $comment)
                                <div class="col-lg-6 col-md-12">
                                    <div class="card-horizontal-product">

                                        <div class="card-horizontal-product-content">
                                            <div class="label-status-comment">
                                                @if($comment->status == 'pending')
                                                    <div class="text-warning">منتظر تایید</div>
                                                @elseif($comment->status == 'accepted')
                                                    <div class="text-success">تایید شده</div>
                                                @else
                                                    <div class="text-danger">تایید نشده</div>
                                                @endif
                                            </div>
                                            <div class="card-horizontal-comment-title">
                                                @if ($comment->commentable)
                                                    <a href="{{ $comment->commentable->link() }}">
                                                        <h3> در {{ $comment->commentable->title }}</h3>
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="card-horizontal-comment">
                                                <p>{!! nl2br(htmlentities($comment->body)) !!}</p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        @else
                            <div class="col-12">
                                <div class="page dt-sl dt-sn pt-3">
                                    <p>چیزی برای نمایش وجود ندارد</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
@endsection
