@extends('layouts.auth_new')
@section('title', config('constants.APP_NAME') . ' - Login')
@section('content')
<div class="container mb-4">
    <div class="row mt-lg-n12 mt-md-n12 mt-n12 justify-content-center">
        <div class="col-xl-4 col-lg-5 col-md-7 mx-auto">
            <div class="card mt-8">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 mb-4">
                    <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1 text-center py-4">
                        <h4 class="font-weight-bolder text-white mt-1">Sign In</h4>
                        <p class="mb-1 text-sm text-white">Enter your email and password to Sign In</p>
                    </div>
                </div>
                <div class="card-body ">

                    <form method="POST" action="{{ route('login.process') }}" role="form" class="text-start">
                        @csrf
                        @if ($errors->any())
                            <div class="p-4 mb-2 text-center text-danger bg-green-100 rounded">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        @if (session('message'))
                            <div class="p-4 mb-2  text-center text-success  bg-green-100 rounded">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="p-4 mb-2 text-center text-success rounded">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="input-group input-group-static mb-4 ">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control validate"  required>
                        </div>
                        <div class="input-group input-group-static mb-4">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control validate" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn bg-gradient-dark w-100 mt-3 mb-0">Sign in</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                    <p class="mb-1 text-sm mx-auto">
                        Don't have an account?
                        <a href="/register" class="text-success text-gradient font-weight-bold">Sign up</a>
                    </p>
                    <p class="mb-4 text-sm mx-auto">

                        <a href="{{route('forgot.password')}}" class="text-success text-gradient font-weight-bold">Forgot Password?</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
