<!-- Start footer -->
<footer class="main-footer dt-sl position-relative">
    <div class="back-to-top">
        <a href="#"><span class="icon"><i class="mdi mdi-chevron-up"></i></span> <span>بازگشت به
                بالا</span></a>
    </div>
    <div class="container main-container">


        <div class="footer-widgets">
            <div class="row">
                @foreach($footer_links as $group)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="widget-menu widget card">
                            <header class="card-header">
                                <h3 class="card-title">{{ option('link_groups_' . $group['key'], $group['name']) }}</h3>
                            </header>
                            <ul class="footer-menu">
                                @foreach($links->where('link_group_id', $group['key']) as $link)
                                    <li>
                                        <a href="{{ $link->link }}">{{ $link->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach

                <div class="col-12 col-md-6 col-lg-3">

                    <div class="symbol footer-logo">

                        @if(option('info_enamad'))
                            {!! option('info_enamad') !!}
                        @endif

                        @if(option('info_samandehi'))
                            {!! option('info_samandehi') !!}
                        @endif

                    </div>
                    <div class="socials">
                        <div class="footer-social">
                            <ul class="text-center">
                                @if(option('social_instagram'))
									<li>
										<a href="{{ option('social_instagram') }}" class="d-flex align-items-center justify-content-center">
										<img src="{{ asset('instagram-logo.png') }}" style="width:24px;height:24px;" alt="instagram"/>
										</a>
									</li>
                                @endif

                                @if(option('social_whatsapp'))
									<li>
										<a href="{{ option('social_whatsapp') }}" class="d-flex align-items-center justify-content-center">
										<img src="{{ asset('whatsapp-logo.png') }}" style="width:24px;height:24px;" alt="whatsapp"/>
										</a>
									</li>
                                @endif

                                @if(option('social_telegram'))
                                    <li>
										<a href="{{ option('social_telegram') }}" class="d-flex align-items-center justify-content-center">
										<img src="{{ asset('telegram-logo.png') }}" style="width:24px;height:24px;" alt="telegram"/>
										</a>
									</li>
                                @endif

                                @if(option('social_facebook'))
                                    <li><a href="{{ option('social_facebook') }}"><i class="mdi mdi-facebook"></i></a></li>
                                @endif

                                @if(option('social_twitter'))
                                    <li><a href="{{ option('social_twitter') }}"><i class="mdi mdi-twitter"></i></a></li>
                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="copyright">
        <div class="container main-container">
            <p class="text-center">
				تمام حقوق این سایت متعلق به شرکت پخش کادور میباشد.راه اندازی و توسعه با:
				
				<a href="https://www.rivasit.com">
					سامانه فروشگاه ساز داستار (شرکت ریواس سیستم پارس)
				</a>
			</p>
        </div>
    </div>
</footer>
<!-- End footer -->
