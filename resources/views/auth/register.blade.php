@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="text-center mb-4">
                            <img src="{{ asset('img/UPTD_PUSKESMAS_WURYANTORO_Logo.png') }}" class="rounded mx-auto d-block h-25 w-25 mb-4" alt="Logo UPTD PUSKESMAS WURYANTORO">
                            <h3>{{ config('app.name', 'Laravel') }}</h3>
                        </div>

                        <h1 class="mx-5 font-weight-bold mt-2 mb-4">Register</h1>

                        <div class="form-group mx-5">
                            <label for="exampleInputEmail1">Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Your Name" autofocus>
                            @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                        <div class="form-group mx-5">
                            <label for="exampleInputEmail1">E-Mail address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter Email">
                            @error('email')
                                  <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                        </div>
                        <div class="form-group mx-5">
                            <label for="exampleInputPassword1">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">

                            @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group mx-5">
                            <label for="exampleInputPassword1">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                        </div>

                        <div class="form-group mx-5 mb-0">
                            <div class="text-center">
                                <button type="submit" class="btn btn-lg btn-primary">REGISTER <svg width="20px" height="20px" viewBox="0 -5 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    
                                    <title>arrow_right [#365]</title>
                                    <desc>Created with Sketch.</desc>
                                    <defs>
                                
                                </defs>
                                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g id="Dribbble-Light-Preview" transform="translate(-340.000000, -6564.000000)" fill="#fff">
                                            <g id="icons" transform="translate(56.000000, 160.000000)">
                                                <path d="M298.803134,6405.67805 L300.227463,6407.16201 C300.5321,6407.48143 300.304126,6407.99811 299.861293,6407.99811 L284.834221,6407.99811 C284.277401,6407.99811 284,6408.4477 284,6409.00043 L284,6408.99242 C284,6409.54515 284.277401,6410.00075 284.834221,6410.00075 L299.846162,6410.00075 C300.291013,6410.00075 300.517977,6410.54047 300.209306,6410.85788 L298.793047,6412.31781 C298.409729,6412.71634 298.426877,6413.35218 298.833396,6413.72867 L298.835413,6413.73268 C299.236888,6414.10517 299.866337,6414.08614 300.244611,6413.68962 L303.449351,6410.34721 C304.186734,6409.57219 304.182699,6408.36059 303.44229,6407.58957 L300.265794,6404.30724 C299.888529,6403.91472 299.264124,6403.8957 298.863658,6404.26518 L298.85357,6404.2752 C298.44806,6404.64869 298.425868,6405.27752 298.803134,6405.67805" id="arrow_right-[#365]">
                                
                                </path>
                                            </g>
                                        </g>
                                    </g>
                                </svg></button>
 
                                {{-- <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button> --}}

                                {{--@if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif--}}
                            </div>
                        </div>

                        <p class="text-center mt-2">Have an account? <a href="{{ route('login') }}">Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
