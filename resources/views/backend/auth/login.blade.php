@extends('layouts.auth.app')
@section('title', $title)
@section('content')
<!--begin:: Main Content -->
<main class="login-box">
    <h1 class="login-logo">
        <a href="/"><b>{{config('app.name')}}</b></a>
    </h1>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">{{$title}}</p>

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
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
                <!--begin::Row-->
                <div class="row">
                    <div class="col-8">
                        <div class="form-check">
                            <input
                                id="remember"
                                type="checkbox"
                                class="form-check-input"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}
                            >

                            <label
                                class="form-check-label"
                                for="remember"
                            >
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Login
                            </button>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!--end::Row-->
            </form>

            <p class="mb-1">
                <a href="/">I forgot my password</a>
            </p>
            <p class="mb-0">
                <a href="{{ route('register') }}" class="text-center">
                    Registration
                </a>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</main>
<!-- /.login-box -->
<!--end:: Main Content-->
@endsection
