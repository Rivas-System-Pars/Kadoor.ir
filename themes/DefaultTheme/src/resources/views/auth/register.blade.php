@extends('front::auth.layouts.master', ['title' => 'ثبت نام در سایت'])

@push('styles')
    <link rel="stylesheet" href="{{ theme_asset('css/vendor/nouislider.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset('css/vendor/nice-select.css') }}">
@endpush

@section('wrapper-classes', 'shopping-page')

@section('content')

    <!-- Start main-content -->
    <main class="main-content dt-sl mt-4 mb-3">
        <div class="container main-container">

            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-7 col-12 mx-auto">
                    <div class="form-ui dt-sl dt-sn pt-4">
                        <div class="section-title title-wide mb-1 no-after-title-wide">
                            <h2 class="font-weight-bold">ثبت نام در سایت</h2>
                        </div>

                        <form id="register-form" action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="form-row-title">
                                <h3>نام</h3>
                            </div>
                            <div class="form-row form-group">
                                <input type="text" name="first_name" class="input-ui pr-2" placeholder="  نام خود را وارد نمایید">
                            </div>
                            <div class="form-row-title">
                                <h3>نام خانوادگی</h3>
                            </div>
                            <div class="form-row form-group">
                                <input type="text" name="last_name" class="input-ui pr-2" placeholder="  نام خانوادگی خود را وارد نمایید">
								@error('last_name')
								<small class="text-danger">{{ $message }}</small>
								@enderror
                            </div>
                            <div class="form-row-title">
                                <h3>شماره موبایل</h3>
                            </div>
                            <div class="form-row with-icon form-group">
                                <input type="text" name="username" class="input-ui pr-2" placeholder="  شماره موبایل خود را وارد نمایید">
                                <i class="mdi mdi-account-circle-outline"></i>
								@error('username')
								<small class="text-danger">{{ $message }}</small>
								@enderror
                            </div>
							<div class="form-row-title">
                                <h3>کد ملی</h3>
                            </div>
                            <div class="form-row form-group">
                                <input type="text" name="notional_code" class="input-ui pr-2" placeholder="  کد ملی خود را وارد نمایید" value="{{ old('notional_code') }}">
								@error('notional_code')
								<small class="text-danger">{{ $message }}</small>
								@enderror
                            </div>
							<div class="form-row-title">
                                <h3>استان</h3>
                            </div>
							<div class="form-row with-icon form-group">
								<div class="custom-select-ui">
									<select class="right" name="province_id" id="province">
									   <option value="">انتخاب کنید</option>
									@foreach ($provinces as $province)
									   <option value="{{ $province->id }}" @if(old('province_id') == $province->id) selected @endif>{{ $province->name }}</option>
									@endforeach
									</select>
								</div>
								@error('province_id')
								<small class="text-danger">{{ $message }}</small>
								@enderror
							</div>
							<div class="form-row-title">
                                <h3>شهر</h3>
                            </div>
							<div class="form-row with-icon form-group">
								<div class="custom-select-ui">
									<select class="right" name="city_id" id="city">
									</select>
								</div>
								@error('city_id')
								<small class="text-danger">{{ $message }}</small>
								@enderror
							</div>
							<div class="form-row-title">
                                <h3>کد پستی</h3>
                            </div>
                            <div class="form-row with-icon form-group">
                                <input type="text" name="zip_code" class="input-ui pr-2" placeholder="کد پستی خود را وارد نمایید" value="{{ old('zip_code') }}">
								@error('zip_code')
								<small class="text-danger">{{ $message }}</small>
								@enderror
                            </div>
							<div class="form-row-title">
                                <h3>آدرس</h3>
                            </div>
                            <div class="form-row with-icon form-group">
								<textarea name="address" class="input-ui pr-2" placeholder="آدرس خود را وارد نمایید">{{ old('address') }}</textarea>
								@error('address')
								<small class="text-danger">{{ $message }}</small>
								@enderror
                            </div>
                            <div class="form-row-title">
                                <h3>رمز عبور</h3>
                            </div>
                            <div class="form-row with-icon form-group">
                                <input id="password" type="password" name="password" class="input-ui pr-2" placeholder="رمز عبور خود را وارد نمایید">
                                <i class="mdi mdi-lock-open-variant-outline"></i>
                            </div>

                            <div class="form-row-title">
                                <h3>تکرار رمز عبور</h3>
                            </div>
                            <div class="form-row form-group">
                                <input type="password" name="password_confirmation" class="input-ui pr-2" placeholder="تکرار رمز عبور خود را وارد نمایید">
                            </div>

                            <div class="form-row mt-4">
                                <div class="col-md-8 col-6">
                                    <div class="form-group">
                                        <input type="text" class="input-ui pl-2 captcha" autocomplete="off" name="captcha" placeholder="کد امنیتی" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <img class="captcha w-100" src="{{ captcha_src('flat') }}" alt="captcha">
                                </div>
                            </div>

                            <div class="form-row mt-3">
                                <button class="btn-primary-cm btn-with-icon mx-auto w-100">
                                    <i class="mdi mdi-account-circle-outline"></i>
                                    ثبت نام در سایت
                                </button>
                            </div>
                            <div class="form-footer text-right mt-3">
                                <span class="d-block font-weight-bold">قبلا ثبت نام کرده اید؟</span>
                                <a href="{{ route('login') }}" class="d-inline-block mr-3 mt-2">وارد شوید</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <!-- End main-content -->

@endsection

@push('scripts')
    <script>
        var redirect_url = '{{ request("redirect") ?: Redirect::intended()->getTargetUrl() }}';
    </script>
    <script src="{{ theme_asset('js/pages/register.js') }}?v=2"></script>
    <script src="{{ theme_asset('js/vendor/wNumb.js') }}"></script>
    <script src="{{ theme_asset('js/vendor/ResizeSensor.min.js') }}"></script>
    <script src="{{ theme_asset('js/vendor/jquery.nice-select.min.js') }}"></script>
    <script src="{{ theme_asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ theme_asset('js/plugins/jquery-validation/localization/messages_fa.min.js') }}?v=2"></script>
	<script>
	$(document).ready(function() {
	  	$('select').niceSelect();
		$(document).on('change', '#province', function (e) {
			$.ajax({
				type: "GET",
				url: "{{ route('provinces.get-cities') }}?id="+$(e.target).val(),
				data: {
					_token: '{{csrf_token()}}',
				},
				success: function(response) {
					response.forEach(function(item){
						$('#city').append('<option value="'+item.id+'">'+item.name+'</option>');
					});
				}
			});
		});
	});
	</script>
@endpush
