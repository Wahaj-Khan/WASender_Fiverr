@extends('frontend.layouts.main')
@section('content')
<main>
    <!--OTP-area-start -->
    <div class="tp-login-area">
        <div class="container-fluid p-0">
            <div class="row gx-0 align-items-center">
                <div class="col-xl-6 col-lg-6 col-12">
                    <div class="tp-login-thumb login-space sky-bg d-flex justify-content-center">
                        <img src="{{ asset('assets/frontend/img/contact/login.jpg') }}" alt="">
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-12">
                    <div class="tp-login-wrapper login-space d-flex justify-content-center">
                        <div id="login" class="tplogin">
                            <div class="tplogin__title">
                                <h3 class="tp-login-title">{{ __('OTP') }}</h3>
                            </div>
                            <div class="tplogin__form">
                                <form method="POST" action="{{ url('otp_verification_func') }}">
                                    @csrf
                                    <div class="tp-mail">
                                        <label for="OTP">{{ __('Your OTP') }}</label>
                                        <input id="otp" type="number" class="form-control" name="otp" required autofocus>

                                        @if(session('error'))
                                        <div class=" alert alert-danger mt-2">
                                            {{ session('error') }}
                                        </div>
                                        @endif
                                    </div>
                                    @if(session('success'))
                                    <div class="alert alert-success mt-2">
                                        {{ session('success') }}
                                    </div>
                                    @endif
                                    <div class="tp-login-button">
                                        <button class="tp-btn-blue-square w-100" type="submit"><span>{{ __('Confirm') }}</span></button>
                                    </div>

                                </form>


                                <form method="POST" action="{{ url('otp_resend') }}">
                                    @csrf
                                    <div class="tp-login-button">
                                        <button class="tp-btn-blue-round w-100 mt-2" id="resend" type="submit"><span>{{ __('Resend OTP') }}</span></button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- OTP-area-end -->
</main>

@endsection