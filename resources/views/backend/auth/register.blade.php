@extends('layouts.auth.app')
@section('title', $title)
@section('content')
<!--begin:: Main Content -->
<main class="register-box">
    <h1 class="register-logo">
        <a href="/"><b>{{config('app.name')}}</b></a>
    </h1>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body register-card-body">
            <p class="register-box-msg">{{$title}}</p>

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf
                <label class="visually-hidden" for="name">Full Name</label>
                <div class="input-group mb-3">
                    <input
                        id="name"
                        type="text"
                        class="form-control"
                        placeholder="Full Name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                    >
                    <div class="input-group-text">
                        <span class="bi bi-envelope"></span>
                    </div>
                </div>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <label class="visually-hidden" for="email">Email</label>
                <div class="input-group mb-3">
                    <input
                        id="email"
                        type="email"
                        class="form-control"
                        placeholder="Email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                    >
                    <div class="input-group-text">
                        <span class="bi bi-envelope"></span>
                    </div>
                </div>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <label class="visually-hidden" for="password">Password</label>
                <div class="input-group mb-3">
                    <input
                        id="password"
                        type="password"
                        class="form-control"
                        placeholder="Password"
                        name="password"
                        required
                    >
                    <div class="input-group-text">
                        <span class="bi bi-lock-fill"></span>
                    </div>
                </div>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <label class="visually-hidden" for="password_confirmation">Confirm Password</label>
                <div class="input-group mb-3">
                    <input
                        id="password_confirmation"
                        type="password"
                        class="form-control"
                        placeholder="Password Confirmation"
                        name="password_confirmation"
                        required
                    >
                    <div class="input-group-text">
                        <span class="bi bi-lock-fill"></span>
                    </div>
                </div>
                @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <!--begin::Row-->
                <div class="row">
                    <div class="col-8">

                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Register
                            </button>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!--end::Row-->
            </form>

            <p class="mb-0">
                <a href="{{ route('login') }}" class="text-center">
                    Login
                </a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</main>
<!-- /.login-box -->
<!--end:: Main Content-->
@endsection
